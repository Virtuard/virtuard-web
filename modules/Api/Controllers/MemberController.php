<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FollowUser;
use App\Models\UserPost;
use App\Models\Ipanorama;
use App\Notifications\PrivateChannelServices;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    protected $user;
    protected $followUser;

    public function __construct()
    {
        $this->user = new User();
        $this->followUser = new FollowUser();
    }

    /**
     * @OA\Get(
     *     path="/api/members",
     *     tags={"Member"},
     *     summary="Get all members (paginated)",
     *     description="Retrieve a paginated list of all members with is_followed field",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="members", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function allMembers(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $perPage = min($perPage, 100); // Max 100 per page

            $query = User::where('role_id', '!=', 1)
                ->where('status', 'publish')
                ->withCount([
                    'followers as followers_count',
                    'followings as following_count'
                ]);

            // Search by name
            if ($request->has('search') && $request->search) {
                $query->where(function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('user_name', 'like', '%' . $request->search . '%');
                });
            }

            // Try multiple ways to get current user ID for Sanctum authentication
            $currentUserId = $request->user() ? $request->user()->id : (Auth::guard('sanctum')->id() ?? Auth::id());
            
            // Exclude current user if authenticated
            if ($currentUserId) {
                $query->where('id', '!=', $currentUserId);
            }

            $members = $query->orderBy('last_login_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->paginate($perPage);

            // Transform collection to add is_followed and is_follower fields
            $members->getCollection()->transform(function ($user) use ($currentUserId) {
                $user->avatar_url = $user->getAvatarUrl();
                
                // Check if current user is following this user
                $user->is_followed = false;
                // Check if this user is following current user (is a follower)
                $user->is_follower = false;
                
                if ($currentUserId) {
                    // Check if current user is following this user
                    $isFollowing = FollowUser::where('user_id', $currentUserId)
                        ->where('follower_id', $user->id)
                        ->exists();
                    $user->is_followed = $isFollowing;
                    
                    // Check if this user is following current user (is a follower of current user)
                    $isFollower = FollowUser::where('user_id', $user->id)
                        ->where('follower_id', $currentUserId)
                        ->exists();
                    $user->is_follower = $isFollower;
                }
                
                return $user;
            });

            return $this->sendSuccess([
                'members' => $members->items(),
                'pagination' => [
                    'current_page' => $members->currentPage(),
                    'per_page' => $members->perPage(),
                    'total' => $members->total(),
                    'last_page' => $members->lastPage(),
                    'from' => $members->firstItem(),
                    'to' => $members->lastItem(),
                    'has_more_pages' => $members->hasMorePages()
                ]
            ], 'Members retrieved successfully');

        } catch (Exception $exception) {
            Log::error("Error getting members: " . $exception->getMessage());
            return $this->sendError('Failed to retrieve members', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/members/{id_or_slug}",
     *     tags={"Member"},
     *     summary="Get member detail",
     *     description="Retrieve detailed information about a member including posts, virtual tours, listings, followers, and following counts",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id_or_slug",
     *         in="path",
     *         required=true,
     *         description="User ID or username",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found"
     *     )
     * )
     */
    public function detailMember(Request $request, $id_or_slug)
    {
        try {
            // Find user by username or ID
            $user = User::where('user_name', $id_or_slug)
                ->orWhere('id', $id_or_slug)
                ->first();

            if (!$user || $user->role_id == 1 || $user->status != 'publish') {
                return $this->sendError('Member not found', [], 404);
            }

            // Try multiple ways to get current user ID for Sanctum authentication
            $currentUserId = $request->user() ? $request->user()->id : (Auth::guard('sanctum')->id() ?? Auth::id());
            
            // Check if current user is following this member
            $isFollowed = false;
            // Check if this member is following current user (is a follower)
            $isFollower = false;
            
            if ($currentUserId) {
                // Check if current user is following this member
                $isFollowed = FollowUser::where('user_id', $currentUserId)
                    ->where('follower_id', $user->id)
                    ->exists();
                
                // Check if this member is following current user (is a follower of current user)
                $isFollower = FollowUser::where('user_id', $user->id)
                    ->where('follower_id', $currentUserId)
                    ->exists();
            }

            // Get posts count and posts
            $postsQuery = UserPost::where('user_id', $user->id);
            $postsCount = $postsQuery->count();
            $posts = $postsQuery->with(['medias', 'ipanorama', 'likes', 'comments'])
                ->orderBy('id', 'DESC')
                ->limit(20)
                ->get()
                ->map(function ($post) {
                    return [
                        'id' => $post->id,
                        'message' => $post->message,
                        'type_status' => $post->type_status,
                        'type_post' => $post->type_post,
                        'created_at' => $post->created_at,
                        'likes_count' => $post->likes->count(),
                        'comments_count' => $post->comments->count(),
                        'medias' => $post->medias->map(function ($media) {
                            return [
                                'id' => $media->id,
                                'file_path' => $media->file_path,
                                'file_url' => get_file_url($media->id, 'full')
                            ];
                        }),
                        'ipanorama' => $post->ipanorama ? [
                            'id' => $post->ipanorama->id,
                            'title' => $post->ipanorama->title,
                            'code' => $post->ipanorama->code
                        ] : null
                    ];
                });

            // Get followers and following counts
            $followersCount = FollowUser::where('follower_id', $user->id)->count();
            $followingCount = FollowUser::where('user_id', $user->id)->count();

            // Get virtual tours (Ipanorama)
            $virtualTours = Ipanorama::where('user_id', $user->id)
                ->where('status', 'publish')
                ->orderBy('id', 'DESC')
                ->get()
                ->map(function ($tour) {
                    return [
                        'id' => $tour->id,
                        'title' => $tour->title,
                        'code' => $tour->code,
                        'thumb' => $tour->thumb ? get_file_url($tour->thumb, 'full') : null,
                        'created_at' => $tour->created_at
                    ];
                });

            // Get listings (hotels, spaces, businesses)
            $allServices = get_bookable_services();
            $listings = [];

            foreach ($allServices as $serviceType => $moduleClass) {
                if (method_exists($moduleClass, 'getVendorServicesQuery')) {
                    $services = $moduleClass::getVendorServicesQuery($user->id)
                        ->where('status', 'publish')
                        ->orderBy('id', 'DESC')
                        ->limit(10)
                        ->get()
                        ->map(function ($service) use ($serviceType) {
                            return [
                                'id' => $service->id,
                                'title' => $service->title ?? $service->name ?? null,
                                'type' => $serviceType,
                                'image' => $service->image_id ? get_file_url($service->image_id, 'full') : null,
                                'created_at' => $service->created_at ?? null
                            ];
                        });
                    
                    if ($services->count() > 0) {
                        $listings[$serviceType] = $services;
                    }
                }
            }

            // Build response
            $data = [
                'member' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'user_name' => $user->user_name,
                    'email' => $user->email,
                    'bio' => $user->bio,
                    'avatar_url' => $user->getAvatarUrl(),
                    'business_name' => $user->business_name,
                    'website_url' => $user->website_url,
                    'instagram_url' => $user->instagram_url,
                    'facebook_url' => $user->facebook_url,
                    'twitter_url' => $user->twitter_url,
                    'linkedin_url' => $user->linkedin_url,
                    'created_at' => $user->created_at,
                ],
                'is_followed' => $isFollowed,
                'is_follower' => $isFollower,
                'stats' => [
                    'posts_count' => $postsCount,
                    'followers_count' => $followersCount,
                    'following_count' => $followingCount,
                    'virtual_tours_count' => $virtualTours->count(),
                    'listings_count' => collect($listings)->flatten()->count()
                ],
                'posts' => $posts,
                'virtual_tours' => $virtualTours,
                'listings' => $listings
            ];

            return $this->sendSuccess($data, 'Member detail retrieved successfully');

        } catch (Exception $exception) {
            Log::error("Error getting member detail: " . $exception->getMessage());
            return $this->sendError('Failed to retrieve member detail', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/members/{id}/follow",
     *     tags={"Member"},
     *     summary="Follow a member",
     *     description="Follow a member by their ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID to follow",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully followed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully followed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request (already following or cannot follow yourself)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found"
     *     )
     * )
     */
    public function follow(Request $request, $id)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $currentUserId = Auth::id();
            $targetUser = User::find($id);

            if (!$targetUser || $targetUser->role_id == 1 || $targetUser->status != 'publish') {
                return $this->sendError('Member not found', [], 404);
            }

            if ($currentUserId == $id) {
                return $this->sendError('Cannot follow yourself', [], 400);
            }

            // Check if already following
            $existingFollow = FollowUser::where('user_id', $currentUserId)
                ->where('follower_id', $id)
                ->first();

            if ($existingFollow) {
                return $this->sendError('Already following this member', [], 400);
            }

            // Create follow relationship
            FollowUser::create([
                'user_id' => $currentUserId,
                'follower_id' => $id
            ]);

            // Send notification
            try {
                $messageData = [
                    'id' => uniqid(),
                    'message' => 'started following you',
                    'attachment' => null
                ];
                $this->notifyUser($targetUser, $messageData, Auth::user());
            } catch (Exception $e) {
                Log::warning("Failed to send follow notification: " . $e->getMessage());
            }

            return $this->sendSuccess([], 'Successfully followed');

        } catch (Exception $exception) {
            Log::error("Error following member: " . $exception->getMessage());
            return $this->sendError('Failed to follow member', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/members/{id}/unfollow",
     *     tags={"Member"},
     *     summary="Unfollow a member",
     *     description="Unfollow a member by their ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID to unfollow",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully unfollowed",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully unfollowed")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request (not following this member)"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Member not found"
     *     )
     * )
     */
    public function unfollow(Request $request, $id)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $currentUserId = Auth::id();
            $targetUser = User::find($id);

            if (!$targetUser || $targetUser->role_id == 1 || $targetUser->status != 'publish') {
                return $this->sendError('Member not found', [], 404);
            }

            // Check if following
            $existingFollow = FollowUser::where('user_id', $currentUserId)
                ->where('follower_id', $id)
                ->first();

            if (!$existingFollow) {
                return $this->sendError('Not following this member', [], 400);
            }

            // Delete follow relationship
            $existingFollow->delete();

            // Send notification
            try {
                $messageData = [
                    'id' => uniqid(),
                    'message' => 'unfollowed you',
                    'attachment' => null
                ];
                $this->notifyUser($targetUser, $messageData, Auth::user());
            } catch (Exception $e) {
                Log::warning("Failed to send unfollow notification: " . $e->getMessage());
            }

            return $this->sendSuccess([], 'Successfully unfollowed');

        } catch (Exception $exception) {
            Log::error("Error unfollowing member: " . $exception->getMessage());
            return $this->sendError('Failed to unfollow member', [], 500);
        }
    }

    /**
     * Send notification to user
     */
    protected function notifyUser($toUser, $message, $currentUser)
    {
        if (!$toUser) return;

        $message_content = __(':name :message', [
            'name' => $currentUser->getDisplayName(),
            'message' => $message['message']
        ]);

        $data = [
            'id' => $message['id'],
            'event' => 'FollowUser',
            'to' => 'user',
            'name' => $currentUser->getDisplayName(),
            'avatar' => $currentUser->getAvatarUrl(),
            'link' => url('/profile/' . ($currentUser->user_name ?? $currentUser->id)),
            'type' => 'follow',
            'message' => $message_content
        ];

        $toUser->notify(new PrivateChannelServices($data));
    }
}
