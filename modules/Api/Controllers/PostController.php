<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FollowUser;
use App\Notifications\PrivateChannelServices;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPost;
use App\Models\PostMedia;
use App\Models\PostLike;
use App\Models\PostComment;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use stdClass;

class PostController extends Controller
{
    protected $userPost;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userPost = new UserPost();
    }

    /**
     * @OA\Get(
     *     path="/api/post",
     *     tags={"Post"},
     *     summary="Search for post",
     *     description="",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="filter post by me, friend or default all",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful search post"
     *     ),
     *  )
     */

     
    public function index(Request $request)
    {
        try {
            $idUser = Auth::id();
            $posts = $this->userPost
                ->withCount('likes')
                ->withCount('comments')
                ->with([
                    'medias',
                    'likes' => function ($query) use ($request, $idUser) {
                        $query->where('user_id',$idUser);
                    },
                    'author.mediaFile'])
                ->when(isset($request->user_id), function ($q) use ($request) {
                    $q->where('user_id', $request->user_id);
                })
                ->orderBy('id', 'desc')
                ->paginate(20)
                ->withQueryString();

            $posts->getCollection()->transform(function ($post) use ($request){
                unset($post->ipanorama_id);
                $post->deletable = $post->user_id == $request->user_id;
                
                $author = $this->selectAuthorFields($post);
                unset($post->author);
                $post->author = $author;
                
                $post->is_liked = $this->isLikedByUser($post);
                unset($post->likes);
                return $post;
            });
            
            return response()->json([
                'status' => true,
                'message' => 'Post received successfully',
                'data' => $posts
            ]);
        } catch (Exception $e) {
            Log::error("Error while fetching posts: ");
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Post received failed',
                'data' => isset($data) ? $data : []
            ], 400);
        }
    }
    
    private function isLikedByUser($post) {
        if($post->likes->count() > 0) return true;
        return false;
    }
    
    private function selectAuthorFields($post) {
        $author = new stdClass();
        $author->id = $post->user_id;
        $author->email = $post->author->email;
        $author->phone = $post->author->phone;
        $author->name = $post->author->name;  
        $author->first_name = $post->author->first_name;
        $author->last_name = $post->author->last_name;
        $author->user_name = $post->author->user_name;
        $author->photo_profile = $post->author->mediaFile
            ? url('/uploads/' . $post->author->mediaFile->file_path)
            : url('/uploads/images/virtuard.png');

        $author->follower_count = User::find($post->author->id)?->followers->count() ?? 0;
        $author->following_count = User::find($post->author->id)?->followings->count() ?? 0;
        return $author;
    }

    /**
     * @OA\Post(
     *     path="/api/post",
     *     tags={"Post"},
     *     summary="Create a post",
     *     description="The user must be authenticated to create a post.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="My new post message"),
     *                 @OA\Property(property="ipanorama_id", type="integer"),
     *                 @OA\Property(property="media_user", type="array", items=@OA\Items(type="file", format="binary"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post created successfully"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post creation failed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid authentication token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        if(isset($request->media_user)){
            $this->validate($request, [
                'message' => 'nullable',
            ]);
        } else {
            $this->validate($request, [
                'message' => 'required',
            ]);
        }

        DB::beginTransaction();
        try {
            $dataPost = [
                'user_id' =>  auth()->user()->id,
                'ipanorama_id' =>  $request->input('ipanorama_id'),
                'message' =>  $request->input('message'),
                'type_status' =>  'Status',
                'type_post' =>  $request->input('type_post'),
                'tag' =>  '-',
            ];
            $post = $this->userPost->create($dataPost);

            if ($request->hasFile('media_user')) {
                $files = $request->file('media_user');
                foreach ($files as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $type = getMimeTypeFromExtension($extension);
                    $path = $file->storeAs('/media', $filename);

                    $dataMedia = [
                        'post_id' => $post->id,
                        'media' => $path,
                        'type' => $type,
                        'is_360_media' => $request->is_360_media,
                    ];
                    $mediaItem = PostMedia::create($dataMedia);
                }
            }

            $post->load('medias');

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Post created failed',
                'error_message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/post/{id}",
     *     tags={"Post"},
     *     summary="Delete a post by ID",
     *     description="Deletes a post. The user must be authenticated and must own the post to delete it.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         ),
     *         description="The ID of the post to delete."
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post deleted successfully."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Bad request."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized - The user does not own the post",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="You are not authorized to delete this post."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found - The post does not exist",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Post not found."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid token",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     ),
     * )
     */
    public function destroy($id)
    {
        try {
            $post = $this->userPost->find($id);

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            if (auth()->user()->isAdmin() || auth()->user()->id == $post->user_id) {
                $post->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Post deleted successfully',
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => 'Post deleted failed',
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Post deleted failed',
            ], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/post/{id}/comment",
     *     tags={"Post"},
     *     summary="Create a comment on a post",
     *     description="Creates a comment on the specified post. The user must be authenticated.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(
     *             type="integer",
     *             example=1
     *         ),
     *         description="The ID of the post to comment on"
     *     ),
     *     @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *        @OA\Property(property="comment",type="string")
     *       )
     *   ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=true
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Comment created successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation errors or creation failed",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="status",
     *                 type="boolean",
     *                 example=false
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Comment creation failed"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid authentication token",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     )
     * )
     */

    public function storeComment(Request $request, $id)
    {
        try {
            $idUser = Auth::id();

            $validated = $request->validate([
                'comment' => [
                    'required',
                    'string',
                    'max:1000',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        if (trim($value) === '') {
                            $fail('The comment cannot be empty or contain only whitespace.');
                        }
                    }
                ],
            ], [
                'comment.required' => 'Comment content is required',
                'comment.string' => 'Comment must be a valid text',
                'comment.max' => 'Comment cannot exceed 1000 characters',
                'comment.min' => 'Comment must be at least 1 character'
            ]);
            
            $post = UserPost::find($id);
            if (!$post) {
                return response()->json([
                    "status" => false,
                    "message" => "Post not found"
                ],404);
            }

            $comment = PostComment::create([
                'post_id' => $id,
                'user_id' => $idUser,
                'comment' => $request->input('comment')
            ]);
            
            $comment->load('user.mediaFile');
            $transformedComment = [
                'id' => $comment->id,
                'post_id' => $comment->post_id,
                'comment' => $comment->comment,
                'created_at' => $comment->created_at,
                'updated_at' => $comment->updated_at,
                'user' => [
                    'id' => $comment->user->id,
                    'phone' => $comment->user->phone,
                    'user_name' => $comment->user->user_name,
                    'name' => $comment->user->name,
                    'created_at' => $comment->user->created_at,
                    'photo_profile' => $comment->user->mediaFile
                        ? url('/uploads/' . $comment->user->mediaFile->file_path)
                        : url('/uploads/images/virtuard.png'),
                    'following_count' => $comment->user->followings->count(),
                    'followers_count' => $comment->user->followers->count(),
                ]
            ];
            
            $messageData = [
                'id' => $post->user_id,
                'message' => 'Commented on your post',
            ];

            $this->notifyUserComment($post->user_id, $messageData, $id);

            return response()->json([
                'status' => true,
                'message' => 'Comment created successfully',
                'data' => $transformedComment
            ]);

        } catch (ValidationException $e) {
            // Get first error message
            $firstError = collect($e->errors())->flatten()->first();

            return response()->json([
                'status' => false,
                'message' => $firstError // Only first error
            ], 422);
        }catch (Exception $e) {
            Log::error("Error while creating comment: ");
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
  

    protected function notifyUserComment($toUserId, $message, $postId)
    {
        $currentUser = auth()->user();
        $toUser = User::find($toUserId);

        if (!$toUser) return;

        $message_content = __(':name :message', [
            'name' => $currentUser->display_name,
            'message' => $message['message']
        ]);

        $data = [
            'id' => $toUserId,
            'notifiable_id' => $toUserId,
            'event' => 'CommentUser',
            'to' => 'user',
            'name' => $currentUser->display_name,
            'avatar' => $currentUser->profile_picture ?? '',
            'link' => url()->previous() . '#Post-' . $postId,
            'type' => 'comment',
            'message' => $message_content
        ];

        $toUser->notify(new PrivateChannelServices($data));
    }
    
    public function getComments($id) {
        try {
            $post = UserPost::find($id);
            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found'
                ], 404);
            }


            $comments = PostComment::with(['user.mediaFile'])
                ->where('post_id', $id)
                ->orderBy('created_at', 'desc')
                ->paginate(20)
                ->withQueryString();
            
            $comments->getCollection()->transform(function ($comment) {
                $commentArray = $comment->toArray();
                unset($commentArray['user_id']);
                unset($commentArray['deleted_at']);
                if (isset($commentArray['user'])) {
                    $commentArray['user'] = [
                        'id' => $commentArray['user']['id'],
                        'name' => $commentArray['user']['name'],
                        'user_name' => $commentArray['user']['user_name'],
                        'phone' => $commentArray['user']['phone'],
                        'photo_profile' => isset($commentArray['user']['media_file']) && $commentArray['user']['media_file']
                            ? url('/uploads/' . $commentArray['user']['media_file']['file_path'])
                            : url('/uploads/images/virtuard.png'),
                        'follower_count' => FollowUser::where('follower_id', $commentArray['user']['id'])->count(),
                        'following_count' => FollowUser::where('user_id', $commentArray['user']['id'])->count(),
                        'created_at' => $commentArray['user']['created_at'],
                    ];
                }

                return $commentArray;
            });

            return response()->json([
                'status' => true,
                'message' => 'Comments fetched successfully',
                'data' => $comments
            ]);
        } catch (Exception $e) {
            Log::error("Error while fetching comments: ");
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    private function createLikePost($idUser, $idPost) {
        $like = new PostLike();
        $like->post_id = $idPost;
        $like->user_id = $idUser;
        $like->save();
        
        return $like;
    }


    public function likeOrUnlikePost($id)
    {
        $id = (int) $id;
        $idUser = Auth::id();
        $post = UserPost::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $postLike = PostLike::where('post_id', $id)
            ->where('user_id', $idUser)
            ->first();

        if (!$postLike) {
            $likedPost = $this->createLikePost($idUser, $id);
            $messageData = [
                'id' => $post->user_id,
                'message' => 'Liked your post',
            ];

            $this->notifyUser($post->user_id, $messageData, $id);
            
            $likedPost->likes_count = PostLike::where("post_id",$id)->count(); // Add current likes count
            return response()->json([
                'status' => true,
                'message' => 'Post liked successfully',
                'data' => [
                    'type' => 'LIKING_POST',
                    'post' => $likedPost
                ]
            ]);
        } 
        
        $postLike->delete();
        $postLike->likes_count = PostLike::where("post_id",$id)->count(); // Add current likes count
        return response()->json([
            'status' => true,
            'message' => 'Post unliked successfully',
            'data' => [
                'type' => 'UNLIKING_POST',
                 'post' => $postLike
            ]
        ]);
    }
    
    public function deletePost($id) {
        try {
            $id = (int) $id;
            $idUser = Auth::id();
            
            $post = UserPost::find($id);
            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found'
                ], 404);
            }
            
            if($post->user_id != $idUser) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to delete this post'
                ], 403);
            }
            
            $post->delete();
            return response()->json([
                'status' => true,
                'message' => 'Post deleted successfully',
            ]); 
            
        }catch(Exception $e) {
            Log::error("Error while deleting post: ");
            Log::error($e->getMessage());
            
            return response()->json([
                'status' => false,
                'message' => 'Error while deleting post',
            ]);
        }
    }

    private function notifyUser($toUserId, $message, $postId)
    {
        $currentUser = auth()->user();
        $toUser = User::find($toUserId);

        if (!$toUser) return;

        $message_content = __(':name :message', [
            'name' => $currentUser->display_name,
            'message' => $message['message']
        ]);

        $data = [
            'id' => $toUserId,
            'notifiable_id' => $toUserId,
            'event' => 'LikeUser',
            'to' => 'user',
            'name' => $currentUser->display_name,
            'avatar' => $currentUser->profile_picture ?? '',
            'link' => url()->previous() . '#Post-' . $postId,
            'type' => 'like',
            'message' => $message_content
        ];

        $toUser->notify(new PrivateChannelServices($data));
    }
}
