<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FollowUser;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPost;
use App\Models\PostMedia;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\Story;
use App\Models\Ipanorama;
use Exception;

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
            $posts = $this->userPost
                ->with(['ipanorama', 'medias', 'likes', 'comments', 'author.mediaFile']) 
                ->when(isset($request->filter), function ($q) use ($request) {
                    if (auth()->check()) {
                        if ($request->filter == 'me') {
                            $q->where('user_id', auth()->user()->id);
                        } elseif ($request->filter == 'friend') {
                            $following_ids = FollowUser::where('user_id', auth()->user()->id)->pluck('follower_id')->toArray();
                            $follower_ids = FollowUser::where('follower_id', auth()->user()->id)->pluck('user_id')->toArray();
                            $ids = array_merge($following_ids, $follower_ids);
                            $q->whereIn('user_id', $ids);
                        }
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(20)
                ->withQueryString();

            $posts->getCollection()->transform(function ($post) {
                if ($post->author) {
                    $post->author->photo_profile = $post->author->mediaFile
                        ? url('/uploads/' . $post->author->mediaFile->file_path)
                        : url('/uploads/images/virtuard.png');
                    unset($post->author->mediaFile); 
                }
                return $post;
            });

            $memberCount = User::count();
            $idUser = Auth::id();
            $dataIpanorama = Ipanorama::where([
                ['user_id', $idUser],
                ['status', 'publish'],
            ])->get();
            $feeds = Story::query()
                ->when(isset($request->filter), function ($q) use ($request) {
                    if (auth()->check()) {
                        if ($request->filter == 'me') {
                            $q->where('user_id', auth()->user()->id);
                        } elseif ($request->filter == 'friend') {
                            $following_ids = FollowUser::where('user_id', auth()->user()->id)->pluck('follower_id')->toArray();
                            $follower_ids = FollowUser::where('follower_id', auth()->user()->id)->pluck('user_id')->toArray();
                            $ids = array_merge($following_ids, $follower_ids);
                            $q->whereIn('user_id', $ids);
                        }
                    }
                })
                ->orderByDesc('id')
                ->paginate(50)
                ->withQueryString();

            $data = [
                'posts' => $posts,
                'memberCount' => $memberCount,
                'dataIpanorama' => $dataIpanorama,
                'feeds' => $feeds,
            ];

            if (auth()->check()) {
                $data['followerCount'] = auth()->user()->followers->count();
                $data['followingCount'] = auth()->user()->followings->count();
            }

            return response()->json([
                'status' => true,
                'message' => 'Post received successfully',
                'data' => $data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Post received failed',
                'data' => isset($data) ? $data : []
            ], 400);
        }
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
     *                 @OA\Property(property="media_user", type="array", items=@OA\Items(type="file"))
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
        $this->validate($request, [
            'message' => 'required',
            'media_user.*' => 'nullable|mimes:jpeg,png,mp4|max:20000',
        ], [
            'media_user.*.mimes' => 'File extention denied',
            'media_user.*.max' => 'Maximum upload file 20MB'
        ]);

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
                    ];
                    $mediaItem = PostMedia::create($dataMedia);
                }
            }

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
        $request->validate([
            'comment' => 'required',
        ]);

        try {
            $data = $request->all();
            $data['user_id'] = auth()->user()->id;
            $data['post_id'] = $id;

            $comment = PostComment::create($data);

            return response()->json([
                'status' => true,
                'message' => 'Coment created successfully',
                'data' => $comment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Coment created failed',
            ], 400);
        }
    }
}
