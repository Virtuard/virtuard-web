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
use App\Helpers\PuzzleArPostHelper;
use App\Models\PuzzleTracking;
use App\Models\UserGameProgress;
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
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter posts by privacy type: 'public', 'friend', or 'private'. Leave empty to get all accessible posts.",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"public", "friend", "private"},
     *             example="public"
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
                ->where(function ($q) use ($idUser, $request) {
                    if ($request->has('type') && $request->type) {
                        $typeFilter = $request->type;
                        if ($typeFilter == 'public') {
                            $q->where(function ($q2) {
                                $q2->where('type_post', 'public')
                                   ->orWhereNull('type_post')
                                   ->orWhere('type_post', '');
                            });
                            if ($idUser) {
                                $q->orWhere('user_id', $idUser);
                            }
                        } elseif ($typeFilter == 'friend') {
                            $q->where(function ($query) use ($idUser) {
                                if ($idUser) {
                                    $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                                    $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                                    $friend_ids = array_merge($following_ids, $follower_ids);
                                    if (!empty($friend_ids)) {
                                        $query->where('type_post', 'friend')
                                              ->whereIn('user_id', $friend_ids);
                                    } else {
                                        $query->where('user_id', 0);
                                    }
                                } else {
                                    $query->where('user_id', 0);
                                }
                            });
                        } elseif ($typeFilter == 'private') {
                            if ($idUser) {
                                $q->where('type_post', 'private')
                                  ->where('user_id', $idUser);
                            } else {
                                $q->where('user_id', 0);
                            }
                        }
                    } else {
                        $q->where(function ($query) use ($idUser) {
                            if ($idUser) {
                                $query->where('user_id', $idUser);
                            }
                            
                            $query->orWhere(function ($q2) {
                                $q2->where('type_post', 'public')
                                   ->orWhereNull('type_post')
                                   ->orWhere('type_post', '');
                            });
                            
                            if ($idUser) {
                                $query->orWhere(function ($q3) use ($idUser) {
                                    $q3->where('type_post', 'friend')
                                       ->where(function ($q4) use ($idUser) {
                                           $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                                           $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                                           $friend_ids = array_merge($following_ids, $follower_ids);
                                           if (!empty($friend_ids)) {
                                               $q4->whereIn('user_id', $friend_ids);
                                           } else {
                                               $q4->where('user_id', 0);
                                           }
                                       });
                                });
                            }
                        });
                    }
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
     *                     property="type",
     *                     type="string",
     *                     description="Post privacy type",
     *                     enum={"public", "friend", "private"},
     *                     example="public"
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
                'type_post' =>  $request->input('type') ?: 'public',
                'tag' =>  '-',
            ];
            $post = $this->userPost->create($dataPost);

            // Share flow: post baru dulu — sisipkan post_id ke link PuzzleAR supaya klik & main tercatat ke post ini
            $messageAfterCreate = PuzzleArPostHelper::appendPostIdToMessage($post->message, $post->id);
            if ($messageAfterCreate !== $post->message) {
                $post->message = $messageAfterCreate;
                $post->save();
            }

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

            $puzzleArUrl = null;
            if ($post->message && (stripos($post->message, 'puzzleAR') !== false)) {
                if (preg_match('#https?://[^\s"\'<>]*puzzleAR[^\s"\'<>]*#i', $post->message, $mm)) {
                    $puzzleArUrl = $mm[0];
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Post created successfully',
                'data' => $post,
                'puzzle_ar_url' => $puzzleArUrl,
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
                 *                     property="type",
                 *                     type="string",
                 *                     description="Post privacy type",
                 *                     enum={"public", "friend", "private"},
                 *                     example="public"
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

            $post = $this->userPost->find($id);

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found.',
                ], 404);
            }

            if ($post->user_id != $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to update this post.',
                ], 403);
            }

            $this->validate($request, [
                'message' => 'nullable',
            ]);

            DB::beginTransaction();
            try {
                $data = [];
                
                $allInput = $request->all();
                if (array_key_exists('message', $allInput)) {
                    $data['message'] = $request->input('message');
                }
                
                if (array_key_exists('ipanorama_id', $allInput)) {
                    $data['ipanorama_id'] = $request->input('ipanorama_id');
                }
                
                if (array_key_exists('type', $allInput)) {
                    $data['type_post'] = $request->input('type') ?: 'public';
                }

                if (!empty($data)) {
                    $post->update($data);
                    $post->refresh();
                }

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

            $comment = PostComment::with('user')->find($id);

            if (!$comment) {
                return response()->json([
                    'status' => false,
                    'message' => 'Comment not found.',
                ], 404);
            }

            $post = UserPost::find($comment->post_id);

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found.',
                ], 404);
            }

            if ($comment->user_id != $userId && $post->user_id != $userId) {
                return response()->json([
                    'status' => false,
                    'message' => 'You are not authorized to delete this comment.',
                ], 403);
            }

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

    /**
     * @OA\Post(
     *     path="/api/post/{id}/track/view",
     *     tags={"PostTracking"},
     *     summary="Track post view",
     *     description="Mencatat bahwa post telah dilihat. Data disimpan ke tabel puzzle_tracking dengan post_id untuk menghubungkan ke post spesifik.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID post (user_post_status.id)",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="View tracked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="View tracked successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="view_count", type="integer", example=42)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function trackView(Request $request, $id)
    {
        $post = UserPost::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        PuzzleTracking::trackView($id, [
            'metadata' => [
                'referrer' => $request->header('referer'),
                'query_params' => $request->all(),
            ]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'View tracked successfully',
            'data' => [
                'view_count' => $post->view_count
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/post/{id}/track/play",
     *     tags={"PostTracking"},
     *     summary="Track post play",
     *     description="Mencatat bahwa user membuka/memainkan game dari post. Data disimpan ke puzzle_tracking dengan event_type='play' dan post_id.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID post (user_post_status.id)",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Play tracked successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Play tracked successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="play_count", type="integer", example=15),
     *                 @OA\Property(property="unique_player_count", type="integer", example=8)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function trackPlay(Request $request, $id)
    {
        $post = UserPost::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        PuzzleTracking::trackPlay($id, [
            'metadata' => [
                'referrer' => $request->header('referer'),
                'query_params' => $request->all(),
            ]
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Play tracked successfully',
            'data' => [
                'play_count' => $post->play_count,
                'unique_player_count' => $post->unique_player_count
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/post/{id}/track/screenshot",
     *     tags={"PostTracking"},
     *     summary="Upload screenshot from game",
     *     description="Upload screenshot hasil game. File disimpan dan tracking disimpan ke puzzle_tracking dengan event_type='screenshot' dan post_id.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID post (user_post_status.id)",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="screenshot",
     *                     type="string",
     *                     format="binary",
     *                     description="Screenshot image file (jpeg, png, jpg, max 5MB)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Screenshot uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Screenshot uploaded successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="screenshot_url", type="string", example="https://virtuard.com/uploads/media/screenshots/screenshot_123.jpg"),
     *                 @OA\Property(property="screenshot_count", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The screenshot field is required."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function uploadScreenshot(Request $request, $id)
    {
        $request->validate([
            'screenshot' => 'required|image|mimes:jpeg,png,jpg|max:5120', // max 5MB
        ]);

        $post = UserPost::find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        try {
            $file = $request->file('screenshot');
            $filename = 'screenshot_' . time() . '_' . $id . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('/media/screenshots', $filename);

            PuzzleTracking::trackScreenshot($id, $path, [
                'metadata' => [
                    'original_filename' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                ]
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Screenshot uploaded successfully',
                'data' => [
                    'screenshot_url' => get_file_url($path) ?? asset('storage/' . $path),
                    'screenshot_count' => $post->screenshot_count
                ]
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to upload screenshot: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/post/{id}/statistics",
     *     tags={"PostTracking"},
     *     summary="Get post tracking statistics",
     *     description="Mengambil statistik lengkap untuk post: view count, play count, unique players, screenshots. Data diambil dari puzzle_tracking dengan filter post_id.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID post (user_post_status.id)",
     *         @OA\Schema(type="integer", example=123)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Statistics retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="post_id", type="integer", example=123),
     *                 @OA\Property(property="view_count", type="integer", example=100),
     *                 @OA\Property(property="play_count", type="integer", example=45),
     *                 @OA\Property(property="unique_player_count", type="integer", example=30),
     *                 @OA\Property(property="screenshot_count", type="integer", example=12),
     *                 @OA\Property(
     *                     property="players",
     *                     type="array",
     *                     description="List of unique players who played",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="user_id", type="integer", example=1),
     *                         @OA\Property(
     *                             property="user",
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="John Doe"),
     *                             @OA\Property(property="avatar", type="string", example="https://...")
     *                         ),
     *                         @OA\Property(property="play_count", type="integer", example=5),
     *                         @OA\Property(property="last_played", type="string", format="date-time", example="2026-03-11T12:00:00.000000Z"),
     *                         @OA\Property(property="total_score", type="integer", example=12500, description="Total score dari user_game_progress")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="screenshots",
     *                     type="array",
     *                     description="List of screenshots uploaded",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=10),
     *                         @OA\Property(property="screenshot_url", type="string", example="https://..."),
     *                         @OA\Property(
     *                             property="user",
     *                             type="object",
     *                             nullable=true,
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="John Doe"),
     *                             @OA\Property(property="avatar", type="string", example="https://...")
     *                         ),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2026-03-11T12:00:00.000000Z")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Post not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Post not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function getStatistics($id)
    {
        $post = UserPost::with(['views.user', 'plays.user', 'screenshots.user'])->find($id);
        if (!$post) {
            return response()->json([
                'status' => false,
                'message' => 'Post not found'
            ], 404);
        }

        $uniquePlayers = PuzzleTracking::where('post_id', $id)
            ->where('event_type', 'play')
            ->whereNotNull('user_id')
            ->with('user')
            ->select('user_id')
            ->distinct()
            ->get()
            ->map(function ($tracking) use ($id) {
                $gameProgress = UserGameProgress::where('user_id', $tracking->user_id)->first();
                
                return [
                    'user_id' => $tracking->user_id,
                    'user' => $tracking->user ? [
                        'id' => $tracking->user->id,
                        'name' => $tracking->user->display_name ?? $tracking->user->name,
                        'avatar' => $tracking->user->getAvatarUrl(),
                    ] : null,
                    'play_count' => PuzzleTracking::where('post_id', $id)
                        ->where('event_type', 'play')
                        ->where('user_id', $tracking->user_id)
                        ->count(),
                    'last_played' => PuzzleTracking::where('post_id', $id)
                        ->where('event_type', 'play')
                        ->where('user_id', $tracking->user_id)
                        ->latest()
                        ->first()
                        ->created_at ?? null,
                    'total_score' => $gameProgress ? $gameProgress->total_score : 0,
                ];
            });

        return response()->json([
            'status' => true,
            'message' => 'Statistics retrieved successfully',
            'data' => [
                'post_id' => $post->id,
                'view_count' => $post->view_count,
                'play_count' => $post->play_count,
                'unique_player_count' => $post->unique_player_count,
                'screenshot_count' => $post->screenshot_count,
                'players' => $uniquePlayers,
                'screenshots' => $post->screenshots->map(function ($tracking) {
                    return [
                        'id' => $tracking->id,
                        'screenshot_url' => get_file_url($tracking->screenshot_url) ?? asset('storage/' . $tracking->screenshot_url),
                        'user' => $tracking->user ? [
                            'id' => $tracking->user->id,
                            'name' => $tracking->user->display_name ?? $tracking->user->name,
                            'avatar' => $tracking->user->getAvatarUrl(),
                        ] : null,
                        'created_at' => $tracking->created_at,
                    ];
                }),
            ]
        ]);
    }
}
