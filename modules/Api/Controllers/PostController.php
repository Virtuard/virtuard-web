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
     *         name="scope",
     *         in="query",
     *         description="Scope of posts to retrieve: 'me' for current user's posts, 'friend' for posts from friends/following. Leave empty or do not send this parameter to get all posts.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"me", "friend"},
     *             example="me"
     *         )
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
                ->whereHas('user')
                ->when(isset($request->scope), function ($q) use ($request, $idUser) {
                    if ($request->scope == 'me') {
                        $q->where('user_id', $idUser);
                    } elseif ($request->scope == 'friend') {
                        $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                        $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                        $ids = array_merge($following_ids, $follower_ids);
                        if (!empty($ids)) {
                            $q->whereIn('user_id', $ids);
                        } else {
                            $q->where('user_id', 0);
                        }
                    }
                })
                ->where(function ($q) use ($idUser) {
                    // User selalu bisa melihat post mereka sendiri, apapun type_post-nya
                    if ($idUser) {
                        $q->where('user_id', $idUser);
                    }
                    
                    // Filter berdasarkan type_post untuk post dari user lain
                    $q->orWhere(function ($query) use ($idUser) {
                        // Public posts: semua bisa lihat (termasuk yang kosong/null)
                        $query->where(function ($q2) {
                            $q2->where('type_post', 'public')
                               ->orWhereNull('type_post')
                               ->orWhere('type_post', '');
                        });
                    })
                    ->orWhere(function ($query) use ($idUser) {
                        // Friend posts: hanya teman yang bisa lihat
                        $query->where('type_post', 'friend')
                              ->where(function ($q2) use ($idUser) {
                                  if ($idUser) {
                                      $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                                      $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                                      $friend_ids = array_merge($following_ids, $follower_ids);
                                      if (!empty($friend_ids)) {
                                          $q2->whereIn('user_id', $friend_ids);
                                      } else {
                                          $q2->where('user_id', 0); // No friends, so no posts
                                      }
                                  } else {
                                      $q2->where('user_id', 0); // Not logged in, can't see friend posts
                                  }
                              });
                    });
                    // Private posts dari user lain tidak bisa dilihat (sudah di-handle di atas dengan where user_id)
                })
                ->orderBy('id', 'desc')
                ->paginate(10)
                ->withQueryString();

            $posts->getCollection()->transform(function ($post) use ($request, $idUser){
                unset($post->ipanorama_id);
                $post->deletable = $post->user_id == $idUser;
                
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
    
    /**
     * @OA\Get(
     *     path="/api/post/{id}",
     *     tags={"Post"},
     *     summary="Get post by ID",
     *     description="Get a single post by its ID. The user must be authenticated.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the post",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $idUser = Auth::id();
            $id = (int) $id;
            
            $post = $this->userPost
                ->withCount('likes')
                ->withCount('comments')
                ->with([
                    'medias',
                    'likes' => function ($query) use ($idUser) {
                        $query->where('user_id', $idUser);
                    },
                    'author.mediaFile'
                ])
                ->whereHas('user')
                ->find($id);
            
            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            // Check if user can access this post based on type_post
            $typePost = $post->type_post ?? 'public';
            $canAccess = false;

            if ($typePost == 'public' || empty($typePost)) {
                $canAccess = true;
            } elseif ($typePost == 'friend') {
                if ($idUser) {
                    $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                    $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                    $friend_ids = array_merge($following_ids, $follower_ids);
                    $canAccess = in_array($post->user_id, $friend_ids) || $post->user_id == $idUser;
                }
            } elseif ($typePost == 'private') {
                $canAccess = $post->user_id == $idUser;
            }

            if (!$canAccess) {
                return response()->json([
                    'status' => false,
                    'message' => 'You do not have permission to view this post'
                ], 403);
            }
            
            // Transform post data
            unset($post->ipanorama_id);
            $post->deletable = $post->user_id == $idUser;
            
            $author = $this->selectAuthorFields($post);
            unset($post->author);
            $post->author = $author;
            
            $post->is_liked = $this->isLikedByUser($post);
            unset($post->likes);
            
            return response()->json([
                'status' => true,
                'message' => 'Post retrieved successfully',
                'data' => $post
            ]);
        } catch (Exception $e) {
            Log::error("Error while fetching post: ");
            Log::error($e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Failed to retrieve post',
                'data' => null
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
     *     description="Creates a new post. The user must be authenticated. Message is required if no media is provided, otherwise optional.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     description="Post message content. Required if media_user is not provided, optional if media_user is provided.",
     *                     example="My new post message"
     *                 ),
     *                 @OA\Property(
     *                     property="ipanorama_id",
     *                     type="integer",
     *                     description="ID of the associated panorama (optional)",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="type_post",
     *                     type="string",
     *                     description="Type of post (optional)",
     *                     example="normal"
     *                 ),
     *                 @OA\Property(
     *                     property="media_user",
     *                     type="array",
     *                     description="Array of media files to upload (optional)",
     *                     @OA\Items(type="file", format="binary")
     *                 ),
     *                 @OA\Property(
     *                     property="is_360_media",
     *                     type="boolean",
     *                     description="Whether the media is 360-degree media (optional, only used when media_user is provided)",
     *                     example=false
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Created post object with medias relationship"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation errors or creation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post created failed"),
     *             @OA\Property(property="error_message", type="string", example="Error details")
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
                'type_post' =>  $request->input('type_post') ?: 'public', // Default to 'public' if empty
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
                        'is_360_media' => $request->is_360_media ?? false,
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
     * @OA\Post(
     *     path="/api/post/{id}/update",
     *     tags={"Post"},
     *     summary="Update a post",
     *     description="Updates an existing post. The user must be authenticated and must own the post to update it. Message is nullable.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The ID of the post to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *            mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
                 *                 @OA\Property(
     *                     property="message",
     *                     type="string",
     *                     description="Post message content (required)",
     *                     example="Updated post message"
     *                 ),
     *                 @OA\Property(
     *                     property="ipanorama_id",
     *                     type="integer",
     *                     description="ID of the associated panorama (optional)",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="type_post",
     *                     type="string",
     *                     description="Type of post (optional)",
     *                     example="normal"
     *                 ),
     *                 @OA\Property(
     *                     property="media_user",
     *                     type="array",
     *                     description="Array of media files to upload (optional). New files will be added to existing media.",
     *                     @OA\Items(type="file", format="binary")
     *                 ),
     *                 @OA\Property(
     *                     property="is_360_media",
     *                     type="boolean",
     *                     description="Whether the media is 360-degree media (optional, only used when media_user is provided)",
     *                     example=false
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="Updated post object with medias relationship"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - validation errors or update failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post update failed"),
     *             @OA\Property(property="error_message", type="string", example="Error details")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - User does not own the post",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to update this post.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found.")
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
    public function update(Request $request, $id)
    {
        try {
            $userId = Auth::id();
            $id = (int) $id;

            // Find the post
            $post = $this->userPost->find($id);

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found.',
                ], 404);
            }

            // Check authorization
            if ($post->user_id != $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to update this post.',
                ], 403);
            }

            // Validation - Only validate message as required
            $this->validate($request, [
                'message' => 'nullable',
            ]);

            DB::beginTransaction();
            try {
                // Collect all fields from request
                $data = [];
                
                // Get message if it exists in request (check using array_key_exists for multipart/form-data)
                $allInput = $request->all();
                if (array_key_exists('message', $allInput)) {
                    $data['message'] = $request->input('message');
                }
                
                // Get ipanorama_id if provided
                if (array_key_exists('ipanorama_id', $allInput)) {
                    $data['ipanorama_id'] = $request->input('ipanorama_id');
                }
                
                // Get type_post if provided
                if (array_key_exists('type_post', $allInput)) {
                    $data['type_post'] = $request->input('type_post') ?: 'public'; // Default to 'public' if empty
                }

                // Update post with collected data
                if (!empty($data)) {
                    $post->update($data);
                    $post->refresh();
                }

                // Handle new media uploads
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
                            'is_360_media' => $request->input('is_360_media') ?? false,
                        ];
                        PostMedia::create($dataMedia);
                    }
                }

                $post->load('medias');

                DB::commit();

                return response()->json([
                    'status' => true,
                    'message' => 'Post updated successfully',
                    'data' => $post
                ]);
            } catch (Exception $e) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Post update failed',
                    'error_message' => $e->getMessage()
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (Exception $e) {
            Log::error("Error while updating post: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
            ], 500);
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
                'post_id' => (int) $comment->post_id,
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

    /**
     * @OA\Delete(
     *     path="/api/post/comment/{id}",
     *     tags={"Post"},
     *     summary="Delete a comment by ID",
     *     description="Deletes a comment. The user must be authenticated and must own the comment or the post to delete it.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the comment to delete",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Comment deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Comment deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Comment not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Comment not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="You are not authorized to delete this comment.")
     *         )
     *     )
     * )
     */
    public function deleteComment($id)
    {
        try {
            $userId = Auth::id();
            $id = (int) $id;

            // Get comment with post relationship
            $comment = PostComment::with('user')->find($id);

            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment not found.',
                ], 404);
            }

            // Get post to check ownership
            $post = UserPost::find($comment->post_id);

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found.',
                ], 404);
            }

            // Check authorization: either comment owner OR post owner
            if ($comment->user_id != $userId && $post->user_id != $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to delete this comment.',
                ], 403);
            }

            // Delete the comment
            $comment->delete();

            return response()->json([
                'status' => true,
                'message' => 'Comment deleted successfully.',
            ]);

        } catch (Exception $e) {
            Log::error("Error while deleting comment: " . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong.',
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
            $idUser = Auth::id();
            $post = UserPost::find($id);
            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found'
                ], 404);
            }


            $comments = PostComment::with(['user.mediaFile'])
                ->join('user_post_status', 'user_post_status.id', '=', 'user_post_comment.post_id')
                ->select('user_post_comment.*', 'user_post_status.user_id as post_user_id')
                ->where('user_post_comment.post_id', $id)
                ->orderBy('user_post_comment.created_at', 'desc')
                ->paginate(20)
                ->withQueryString();
            
            $comments->getCollection()->transform(function ($comment) use($idUser) {
                $commentArray = $comment->toArray();
                $commentArray['deletable'] = $this->isDeletable($commentArray, $idUser);
                unset($commentArray['post_user_id']);
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
    
    private function isDeletable($comment, $idUser){
        if($comment['post_user_id'] == $idUser || $comment['user_id'] == $idUser)  return true;
        
        return false;
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
