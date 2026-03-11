<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\FollowUser;
use App\Models\PostCompletion;
use App\Notifications\PrivateChannelServices;
use App\Notifications\RecordNotification;
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
                        $query->where('user_id', $idUser);
                    },
                    'author.mediaFile'
                ])
                ->when($request->has('type'), function ($q) use ($request, $idUser) {
                    $type = $request->query('type');
                    if ($type == 'public') {
                        $q->where('public', 1);
                    } elseif ($type == 'friend') {
                        $q->where('public', 2);
                    } elseif ($type == 'private') {
                        // Users can only see their own private posts
                        $q->where('public', 0)->where('user_id', $idUser);
                    }
                })
                ->when($request->has('scope'), function ($q) use ($request, $idUser) {
                    if ($request->scope == 'me') {
                        $q->where('user_id', $idUser);
                    } elseif ($request->scope == 'friend') {
                        $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                        $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                        $ids = array_unique(array_merge($following_ids, $follower_ids));

                        if (!empty($ids)) {
                            $q->whereIn('user_id', $ids)->where(function ($query) {
                                $query->whereIn('public', [1, 2])->orWhereNull('public');
                            });
                        } else {
                            $q->where('user_id', 0);
                        }
                    }
                })
                ->when(!$idUser && !$request->has('scope') && !$request->has('type'), function ($q) {
                    // Default: Only show public posts (public = 1 or null) for guests
                    $q->where(function ($query) {
                        $query->where('public', 1)->orWhereNull('public');
                    });
                })
                ->orderBy('id', 'desc')
                ->paginate($request->input('per_page', 20))
                ->withQueryString();

            $posts->getCollection()->transform(function ($post) use ($request, $idUser) {
                unset($post->ipanorama_id);
                $post->deletable = $post->user_id == $idUser;

                $author = $this->selectAuthorFields($post);
                unset($post->author);
                $post->author = $author;

                $post->is_liked = $this->isLikedByUser($post);
                unset($post->likes);

                // Inject Statistics: Try user_post_status first, then legacy posts table
                $post->plays = (int) ($post->plays_count ?? 0);
                $post->views = (int) ($post->views_count ?? 0);
                $post->completions = (int) ($post->completions_count ?? 0);

                // Fallback to legacy posts table if counts are 0
                if ($post->plays == 0) {
                    $mediaName = '';
                    if (!empty($post->media)) {
                        $mediaName = basename($post->media);
                    } elseif ($post->medias && $post->medias->isNotEmpty()) {
                        // Check first media from relationship if main column is empty
                        $mediaName = basename($post->medias->first()->media);
                    }

                    if (!empty($mediaName)) {
                        try {
                            $stats = DB::table('posts')
                                ->where('image_url', 'like', '%' . $mediaName . '%')
                                ->select('plays', 'completions')
                                ->first();

                            if ($stats) {
                                $post->plays = (int) $stats->plays;
                                $post->completions = (int) $stats->completions;
                            }
                        } catch (\Exception $e) {
                        }
                    }
                }

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
                ->find($id);

            if (!$post) {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found'
                ], 404);
            }

            // Visibility Check
            $canSee = false;
            if ($post->public == 1) {
                // Public post
                $canSee = true;
            } elseif ($idUser && $post->user_id == $idUser) {
                // Owner can always see
                $canSee = true;
            } elseif ($post->public == 2 && $idUser) {
                // Friend post (public = 2)
                $isFollowing = FollowUser::where('user_id', $idUser)->where('follower_id', $post->user_id)->exists();
                $isFollowedBy = FollowUser::where('user_id', $post->user_id)->where('follower_id', $idUser)->exists();
                if ($isFollowing || $isFollowedBy) {
                    $canSee = true;
                }
            }

            if (!$canSee) {
                if ($post->public == 0) {
                    return response()->json([
                        'status' => false,
                        'message' => 'This post is private'
                    ], 403);
                }
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

            // Inject Statistics from the posts table
            $post->plays = (int) ($post->plays_count ?? 0);
            $post->views = (int) ($post->views_count ?? 0);
            $post->completions = (int) ($post->completions_count ?? 0);

            if ($post->plays == 0) {
                $mediaName = '';
                if (!empty($post->media)) {
                    $mediaName = basename($post->media);
                } elseif ($post->medias && $post->medias->isNotEmpty()) {
                    $mediaName = basename($post->medias->first()->media);
                }

                if (!empty($mediaName)) {
                    try {
                        $stats = DB::table('posts')
                            ->where('image_url', 'like', '%' . $mediaName . '%')
                            ->select('plays', 'completions')
                            ->first();

                        if ($stats) {
                            $post->plays = (int) $stats->plays;
                            $post->completions = (int) $stats->completions;
                        }
                    } catch (\Exception $e) {
                        // Ignore
                    }
                }
            }

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

    private function isLikedByUser($post)
    {
        if ($post->likes->count() > 0)
            return true;
        return false;
    }

    private function selectAuthorFields($post)
    {
        $author = new stdClass();
        $author->id = $post->user_id;

        if ($post->author) {
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
        } else {
            $author->email = null;
            $author->phone = null;
            $author->name = 'Unknown User';
            $author->first_name = 'Unknown';
            $author->last_name = 'User';
            $author->user_name = 'unknown';
            $author->photo_profile = url('/uploads/images/virtuard.png');
            $author->follower_count = 0;
            $author->following_count = 0;
        }

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
        if (isset($request->media_user)) {
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
            // Support 'type' as synonym for 'type_post'
            $typePost = $request->input('type') ?? $request->input('type_post');

            // Map privacy type to 'public' database column
            // public=1 (Public), public=2 (Friend), public=0 (Private)
            $isPublic = 1; // Default
            if ($typePost == 'private' || $typePost == 'me' || $request->input('public') === '0') {
                $isPublic = 0;
            } elseif ($typePost == 'friend' || $request->input('public') === '2') {
                $isPublic = 2;
            } elseif ($typePost == 'public' || $request->input('public') === '1') {
                $isPublic = 1;
            }

            $dataPost = [
                'user_id' => auth()->user()->id,
                'ipanorama_id' => $request->input('ipanorama_id'),
                'message' => $request->input('message'),
                'type_status' => 'Status',
                'type_post' => $typePost,
                'public' => $isPublic,
                'tag' => '-'
            ];
            $post = $this->userPost->create($dataPost);

            if ($request->hasFile('media_user')) {
                $files = $request->file('media_user');
                if (!is_array($files)) {
                    $files = [$files];
                }

                // Ensure the uploads/media directory exists and is writable
                $mediaDir = public_path('uploads/media');
                if (!file_exists($mediaDir)) {
                    mkdir($mediaDir, 0775, true);
                }

                foreach ($files as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $type = getMimeTypeFromExtension($extension);
                    $path = $file->storeAs('/media', $filename, 'uploads');

                    if ($path === false || $path == 0) {
                        \Illuminate\Support\Facades\Log::error('[PostController] storeAs failed for file: ' . $filename . ' — check permissions on ' . $mediaDir);
                    }

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

                // Get message if it exists in request
                $allInput = $request->all();
                if (array_key_exists('message', $allInput)) {
                    $data['message'] = $request->input('message');
                }

                // Get ipanorama_id if provided
                if (array_key_exists('ipanorama_id', $allInput)) {
                    $data['ipanorama_id'] = $request->input('ipanorama_id');
                }

                // Support 'type' or 'type_post' for privacy updates
                $typePost = $request->input('type') ?? $request->input('type_post');
                if ($typePost) {
                    $data['type_post'] = $typePost;
                    if ($typePost == 'private' || $typePost == 'me') {
                        $data['public'] = 0;
                    } elseif ($typePost == 'friend') {
                        $data['public'] = 2;
                    } elseif ($typePost == 'public') {
                        $data['public'] = 1;
                    }
                }

                // Prioritize explicit 'public' or 'is_public' flags
                if (array_key_exists('public', $allInput)) {
                    $data['public'] = $request->input('public');
                } elseif (array_key_exists('is_public', $allInput)) {
                    $data['public'] = $request->input('is_public');
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
                ], 404);
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
        } catch (Exception $e) {
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

        if (!$toUser)
            return;

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

    public function getComments($id)
    {
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

            $comments->getCollection()->transform(function ($comment) use ($idUser) {
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

    private function isDeletable($comment, $idUser)
    {
        if ($comment['post_user_id'] == $idUser || $comment['user_id'] == $idUser)
            return true;

        return false;
    }
    private function createLikePost($idUser, $idPost)
    {
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

            $likedPost->likes_count = PostLike::where("post_id", $id)->count(); // Add current likes count
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
        $postLike->likes_count = PostLike::where("post_id", $id)->count(); // Add current likes count
        return response()->json([
            'status' => true,
            'message' => 'Post unliked successfully',
            'data' => [
                'type' => 'UNLIKING_POST',
                'post' => $postLike
            ]
        ]);
    }

    public function deletePost($id)
    {
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

            if ($post->user_id != $idUser) {
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

        } catch (Exception $e) {
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

        if (!$toUser)
            return;

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
    public function getPuzzleStats(Request $request)
    {
        Log::info('[PostController] getPuzzleStats hit with puzzle_id: ' . ($request->query('puzzle_id') ?? 'null'));
        $puzzleId = $request->query('puzzle_id') ?? '';
        if (empty($puzzleId)) {
            return response()->json(['status' => 'error', 'message' => 'puzzle_id is required'], 400);
        }

        $postIdInt = (int) $puzzleId;
        if ($postIdInt <= 0) {
            // First check if it's a modern post in post_media
            $linkedPostId = DB::table('post_media')
                ->where('media', 'LIKE', "%$puzzleId%")
                ->value('post_id');
                
            if ($linkedPostId) {
                $postIdInt = (int) $linkedPostId;
            } else {
                // Fallback to check legacy posts table
                $row = DB::table('posts')
                    ->where('image_url', 'LIKE', "%$puzzleId%")
                    ->orWhere('puzzle_id', $puzzleId)
                    ->first(['id']);
                if ($row) {
                    $postIdInt = (int) $row->id;
                }
            }
        }

        $plays = 0;
        $wins = 0;
        $shareCount = 0;
        $leaderboard = [];
        $recentPlayers = [];
        $sharers = [];

        if ($postIdInt > 0) {
            // Priority 1: Check new system
            $post = DB::table('user_post_status')->where('id', $postIdInt)->first(['plays_count', 'completions_count', 'plays', 'completions']);
            if ($post) {
                $plays = (int) ($post->plays_count ?? $post->plays ?? 0);
                $wins = (int) ($post->completions_count ?? $post->completions ?? 0);
            }

            // Priority 2: Fallback or augment with legacy system if counts are 0 or not found
            if ($plays == 0) {
                $legacyPost = DB::table('posts')->where('id', $postIdInt)->first(['plays', 'completions', 'shares']);

                // If not found by ID, try finding by puzzle_id/filename if the input was a string
                if (!$legacyPost && !is_numeric($puzzleId)) {
                    $legacyPost = DB::table('posts')
                        ->where('image_url', 'LIKE', "%$puzzleId%")
                        ->orWhere('puzzle_id', $puzzleId)
                        ->first(['id', 'plays', 'completions', 'shares']);
                }

                if ($legacyPost) {
                    $plays = (int) $legacyPost->plays;
                    $wins = (int) $legacyPost->completions;
                    $shareCount = (int) ($legacyPost->shares ?? 0);
                    // Update internal ID if we found it via filename
                    if (isset($legacyPost->id))
                        $postIdInt = (int) $legacyPost->id;
                }
            }

            $leaderboard = DB::table('post_completions as pc')
                ->leftJoin('users as u', 'pc.user_id', '=', 'u.id')
                ->leftJoin('user_game_progress as ugp', 'u.id', '=', 'ugp.user_id')
                ->where('pc.post_id', $postIdInt)
                ->orderBy('pc.time_spent', 'asc')
                ->orderBy('pc.moves', 'asc')
                ->limit(10)
                ->get(['pc.time_spent', 'pc.moves', 'pc.created_at as date', 'pc.user_id', 'u.name', 'u.username', 'u.photo_profile', 'u.avatar_url', 'u.bio', 'ugp.current_level', 'ugp.coins', 'ugp.total_score'])
                ->map(function ($row) {
                    $userName = !empty($row->name) ? $row->name : (!empty($row->username) ? $row->username : 'Unknown');

                    // Stats with bio fallback
                    $level = (int) ($row->current_level ?? 0);
                    $coins = (int) ($row->coins ?? 0);
                    if ($level <= 0 && !empty($row->bio)) {
                        if (preg_match('/\[VARD:LV(\d+)/', $row->bio, $m))
                            $level = (int) $m[1];
                        if (preg_match('/\|C(\d+)/', $row->bio, $m))
                            $coins = (int) $m[1];
                    }
                    if ($level <= 0)
                        $level = 1;

                    return [
                        'username' => $userName,
                        'user_id' => (int) $row->user_id,
                        'avatar_url' => !empty($row->avatar_url) ? $row->avatar_url : $row->photo_profile,
                        'time_spent' => (int) $row->time_spent,
                        'moves' => (int) $row->moves,
                        'date' => $row->date,
                        'current_level' => $level,
                        'coins' => $coins,
                        'total_score' => (int) ($row->total_score ?? $coins)
                    ];
                });

            $recentPlayers = DB::table('post_plays as pp')
                ->leftJoin('users as u', 'pp.user_id', '=', 'u.id')
                ->leftJoin('user_game_progress as ugp', 'u.id', '=', 'ugp.user_id')
                ->where('pp.post_id', $postIdInt)
                ->orderBy('pp.created_at', 'desc')
                ->limit(15)
                ->get(['pp.created_at as date', 'pp.user_id', 'u.name', 'u.username', 'u.photo_profile', 'u.avatar_url', 'u.bio', 'ugp.current_level', 'ugp.coins'])
                ->map(function ($row) {
                    $userName = !empty($row->name) ? $row->name : (!empty($row->username) ? $row->username : 'Unknown');

                    $level = (int) ($row->current_level ?? 0);
                    if ($level <= 0 && !empty($row->bio)) {
                        if (preg_match('/\[VARD:LV(\d+)/', $row->bio, $m))
                            $level = (int) $m[1];
                    }
                    if ($level <= 0)
                        $level = 1;

                    return [
                        'username' => $userName,
                        'user_id' => (int) $row->user_id,
                        'avatar_url' => !empty($row->avatar_url) ? $row->avatar_url : $row->photo_profile,
                        'date' => $row->date,
                        'current_level' => $level,
                        'coins' => (int) ($row->coins ?? 0)
                    ];
                });

            $sharers = DB::table('post_shares as ps')
                ->leftJoin('users as u', 'ps.user_id', '=', 'u.id')
                ->leftJoin('user_game_progress as ugp', 'u.id', '=', 'ugp.user_id')
                ->where('ps.post_id', $postIdInt)
                ->orderBy('ps.created_at', 'desc')
                ->limit(15)
                ->get(['ps.created_at as date', 'ps.user_id', 'u.name', 'u.username', 'u.photo_profile', 'u.avatar_url', 'u.bio', 'ugp.current_level', 'ugp.coins'])
                ->map(function ($row) {
                    $userName = !empty($row->name) ? $row->name : (!empty($row->username) ? $row->username : 'Unknown');

                    $level = (int) ($row->current_level ?? 0);
                    if ($level <= 0 && !empty($row->bio)) {
                        if (preg_match('/\[VARD:LV(\d+)/', $row->bio, $m))
                            $level = (int) $m[1];
                    }
                    if ($level <= 0)
                        $level = 1;

                    return [
                        'username' => $userName,
                        'user_id' => (int) $row->user_id,
                        'avatar_url' => !empty($row->avatar_url) ? $row->avatar_url : $row->photo_profile,
                        'date' => $row->date,
                        'current_level' => $level,
                        'coins' => (int) ($row->coins ?? 0)
                    ];
                });
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'puzzle_id' => $puzzleId,
                'total_plays' => $plays,
                'total_wins' => $wins,
                'total_shares' => $shareCount,
                'avg_moves' => 0,
                'leaderboard' => $leaderboard,
                'recent_players' => $recentPlayers,
                'sharers' => $sharers
            ]
        ]);
    }

    public function getPuzzleDetails(Request $request)
    {
        $puzzleId = $request->query('id') ?? '';
        if (empty($puzzleId)) {
            return response()->json(['status' => 'error', 'message' => 'id is required'], 400);
        }

        $row = DB::table('shared_puzzles')
            ->where('puzzle_id', $puzzleId)
            ->first(['image_url', 'upload_time']);

        if ($row) {
            return response()->json([
                'status' => 'success',
                'puzzle_id' => $puzzleId,
                'image_url' => $row->image_url,
                'upload_time' => $row->upload_time
            ]);
        } else {
            return response()->json(['status' => 'error', 'message' => 'Puzzle not found'], 404);
        }
    }

    /**
     * Increment post statistics (plays, completions)
     */
    public function reportStats(Request $request, $id)
    {
        try {
            $type = $request->input('type', 'play'); // 'play' or 'win'
            $userId = Auth::id();

            // Handle cases where $id might be a legacy posts.id or a filename/puzzle_id string
            $post = UserPost::find((int) $id);
            $legacyPost = null;

            if (!$post) {
                // Not a direct UserPost ID. 
                // First, if it's a string filename, try to find the modern UserPost via post_media
                if (!is_numeric($id)) {
                    $linkedPostId = DB::table('post_media')
                        ->where('media', 'LIKE', "%$id%")
                        ->value('post_id');
                        
                    if ($linkedPostId) {
                        $post = UserPost::find($linkedPostId);
                    }
                }

                if (!$post) {
                    // If still no UserPost found, try finding a legacy post
                    $legacyPost = DB::table('posts')->where('id', (int) $id)->first();
                    
                    // If not found by ID, it might be a filename/puzzle_id string passed from tablet/mobile
                    if (!$legacyPost && !is_numeric($id)) {
                        $legacyPost = DB::table('posts')
                            ->where('image_url', 'LIKE', "%$id%")
                            ->orWhere('puzzle_id', $id)
                            ->first();
                    }

                    // If we found a legacy post, try to find the linked UserPost by matching the image name
                    if ($legacyPost) {
                        $mediaName = basename($legacyPost->image_url);
                        if (!empty($mediaName)) {
                            // Look for a UserPost that has this media
                            $linkedPostId = DB::table('post_media')
                                ->where('media', 'LIKE', "%$mediaName%")
                                ->value('post_id');
                                
                            if ($linkedPostId) {
                                $post = UserPost::find($linkedPostId);
                            }
                        }
                        
                        // Update internal ID reference if we found the legacy post
                        $id = $legacyPost->id;
                    }
                }
            }

            // We now have either $post (UserPost), $legacyPost, or both.
            // Record the play/completion transaction first
            $transactionRecorded = false;
            
            // For transactions, we prefer the UserPost ID if available, otherwise fallback to legacy ID
            $transactionId = $post ? $post->id : $id; 

            try {
                if ($type === 'win') {
                    DB::table('post_completions')->insert([
                        'post_id' => $transactionId,
                        'user_id' => $userId,
                        'time_spent' => $request->input('time_spent', 0),
                        'moves' => $request->input('moves', 0),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $transactionRecorded = true;

                    // Check for Record Breaking (Using transactionId)
                    $bestTime = DB::table('post_completions')
                        ->where('post_id', $transactionId)
                        ->where('user_id', '!=', $userId)
                        ->min('time_spent');

                    $currentTime = (int) $request->input('time_spent', 0);
                    if ($currentTime > 0 && ($bestTime === null || $currentTime < $bestTime)) {
                        // New Record!
                        $authorId = $post ? $post->user_id : ($legacyPost ? $legacyPost->user_id : null);
                        if ($authorId) {
                            $author = User::find($authorId);
                            if ($author && $author->id != $userId) {
                                $author->notify(new RecordNotification([
                                    'id' => uniqid(),
                                    'name' => Auth::user()->display_name ?? 'A user',
                                    'avatar' => Auth::user()->avatar_url ?? '',
                                    'link' => url('/post/' . $transactionId),
                                    'message' => (Auth::user()->display_name ?? 'Someone') . ' broke the record on your puzzle!',
                                    'puzzle_id' => $transactionId,
                                    'time_spent' => $currentTime,
                                    'moves' => $request->input('moves', 0),
                                ]));
                            }
                        }
                    }
                } else {
                    DB::table('post_plays')->insert([
                        'post_id' => $transactionId,
                        'user_id' => $userId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $transactionRecorded = true;
                }
            } catch (\Exception $e) {
                Log::error("[PostController] Failed to record transaction: " . $e->getMessage());
            }

            // Now increment the aggregate counters
            $currentPlays = 0;
            $currentWins = 0;

            if ($post) {
                // Increment on modern user_post_status table
                if ($type === 'win') {
                    $post->increment('completions_count');
                    $post->increment('completions'); // Backwards compatibility for the model
                } else {
                    $post->increment('plays_count');
                    $post->increment('plays'); // Backwards compatibility for the model
                }
                
                $currentPlays = (int) ($post->plays_count ?? $post->plays);
                $currentWins = (int) ($post->completions_count ?? $post->completions);
                
                // Also update the legacy table if it exists to keep them in sync
                if ($legacyPost) {
                    try {
                        if ($type === 'win') {
                            DB::table('posts')->where('id', $legacyPost->id)->increment('completions');
                        } else {
                            DB::table('posts')->where('id', $legacyPost->id)->increment('plays');
                        }
                    } catch (\Exception $syncErr) {}
                }
            } elseif ($legacyPost) {
                // Increment purely on legacy posts table
                try {
                    if ($type === 'win') {
                        DB::table('posts')->where('id', $legacyPost->id)->increment('completions');
                    } else {
                        DB::table('posts')->where('id', $legacyPost->id)->increment('plays');
                    }

                    $updated = DB::table('posts')->where('id', $legacyPost->id)->first();
                    $currentPlays = (int) ($updated->plays ?? 0);
                    $currentWins = (int) ($updated->completions ?? 0);
                } catch (\Exception $e) {
                    Log::error("[PostController] Legacy increment failed: " . $e->getMessage());
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Post not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Statistics updated successfully',
                'data' => [
                    'id' => $transactionId,
                    'type' => $type,
                    'current_plays' => $currentPlays,
                    'current_wins' => $currentWins,
                    'legacy' => !$post && $legacyPost
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to update statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
}


