<?php

namespace Modules\Api\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\FollowUser;

class FollowController
{
    public function getFollowers(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Validate pagination parameters
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Validate per_page limit
            if ($perPage > 100) {
                $perPage = 100;
            }
            
            $userId = $request->route('id');

            // Verify that the target user exists and is active (excluding admin)
            $targetUser = DB::table('users')
                ->where('id', $userId)
                ->where('role_id', '!=', 1) // Exclude admin
                ->where('status', 'publish')
                ->first();

            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or inactive',
                    'data' => [
                        'followers' => [],
                        'pagination' => [
                            'current_page' => 1,
                            'per_page' => $perPage,
                            'total' => 0,
                            'last_page' => 1,
                            'from' => null,
                            'to' => null,
                            'has_more_pages' => false
                        ]
                    ]
                ], 404);
            }

            // Query to get followers with JOIN
            $followings = DB::table('follow_member')
                ->join('users', 'follow_member.user_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.user_name',
                    'users.avatar_id',
                    'users.created_at'
                )
                ->where('follow_member.follower_id', $userId)
                ->where('users.role_id', '!=', 1) // Exclude admin
                ->where('users.status', 'publish') // Only active users
                ->orderBy('follow_member.created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Add avatar_url and follow status to each follower
            // Try multiple ways to get current user ID
            $currentUserId = $request->user() ? $request->user()->id : (Auth::guard('sanctum')->id() ?? Auth::id());
            
            // Check if current user is following the target user (userId)
            $isFollowingTarget = false;
            if ($currentUserId) {
                $isFollowingTarget = FollowUser::where('user_id', $currentUserId)
                    ->where('follower_id', $userId)
                    ->exists();
            }
            
            $transformedItems = collect($followings->items())->map(function ($user) use ($currentUserId, $userId, $isFollowingTarget) {
                $user->avatar_url = get_file_url($user->avatar_id, 'full');
                // Get followers count for this user
                $user->followers_count = DB::table('follow_member')
                    ->where('follower_id', $user->id)
                    ->count();

                // Get followings count for this user
                $user->followings_count = DB::table('follow_member')
                    ->where('user_id', $user->id)
                    ->count();
                
                // Check if current user is following this user (the follower in the list)
                $user->is_followed = false;
                // Check if this user (the follower) is following current user
                // Note: In getFollowers context, this user is already a follower of the target user
                // So if current user is this user, then is_follower should be true (they follow the target)
                $user->is_follower = false;
                
                if ($currentUserId) {
                    // If current user is viewing their own followers, mark themselves
                    if ($currentUserId == $user->id) {
                        $user->is_follower = true; // They are a follower of the target user
                    }
                    
                    // Check if current user is following this user (the follower in the list)
                    $isFollowing = FollowUser::where('user_id', $currentUserId)
                        ->where('follower_id', $user->id)
                        ->exists();
                    $user->is_followed = $isFollowing;
                    
                    // Check if this user (the follower) is following current user
                    if ($currentUserId != $user->id) {
                        $isFollower = FollowUser::where('user_id', $user->id)
                            ->where('follower_id', $currentUserId)
                            ->exists();
                        $user->is_follower = $isFollower;
                    }
                }
                
                // Remove avatar_id from response as it's not needed
                unset($user->avatar_id);
                return $user;
            })->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Followers retrieved successfully',
                'data' => [
                    'followers' => $transformedItems,
                    'is_following_target' => $isFollowingTarget, // Whether current user is following the target user
                    'pagination' => [
                        'current_page' => $followings->currentPage(),
                        'per_page' => $followings->perPage(),
                        'total' => $followings->total(),
                        'last_page' => $followings->lastPage(),
                        'from' => $followings->firstItem(),
                        'to' => $followings->lastItem(),
                        'has_more_pages' => $followings->hasMorePages()
                    ]
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve followers',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFollowings(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Validate pagination parameters
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Validate per_page limit
            if ($perPage > 100) {
                $perPage = 100;
            }
            
            $userId = $request->route('id');
            
            // Verify that the target user exists and is active (excluding admin)
            $targetUser = DB::table('users')
                ->where('id', $userId)
                ->where('role_id', '!=', 1) // Exclude admin
                ->where('status', 'publish')
                ->first();

            if (!$targetUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found or inactive',
                    'data' => [
                        'followings' => [],
                        'pagination' => [
                            'current_page' => 1,
                            'per_page' => $perPage,
                            'total' => 0,
                            'last_page' => 1,
                            'from' => null,
                            'to' => null,
                            'has_more_pages' => false
                        ]
                    ]
                ], 404);
            }
            
            // Query to get followings with JOIN
            $followings = DB::table('follow_member')
                ->join('users', 'follow_member.follower_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.user_name',
                    'users.avatar_id',
                    'users.created_at'
                )
                ->where('follow_member.user_id', $userId)
                ->where('users.role_id', '!=', 1) // Exclude admin
                ->where('users.status', 'publish') // Only active users
                ->orderBy('follow_member.created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Add avatar_url and follow status to each following
            // Try multiple ways to get current user ID
            $currentUserId = $request->user() ? $request->user()->id : (Auth::guard('sanctum')->id() ?? Auth::id());
            $transformedItems = collect($followings->items())->map(function ($user) use ($currentUserId) {
                $user->avatar_url = get_file_url($user->avatar_id, 'full');
                // Get followers count for this user
                $user->followers_count = DB::table('follow_member')
                    ->where('follower_id', $user->id)
                    ->count();

                // Get followings count for this user
                $user->followings_count = DB::table('follow_member')
                    ->where('user_id', $user->id)
                    ->count();
                
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
                
                // Remove avatar_id from response as it's not needed
                unset($user->avatar_id);
                return $user;
            })->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Followings retrieved successfully',
                'data' => [
                    'followings' => $transformedItems,
                    'pagination' => [
                        'current_page' => $followings->currentPage(),
                        'per_page' => $followings->perPage(),
                        'total' => $followings->total(),
                        'last_page' => $followings->lastPage(),
                        'from' => $followings->firstItem(),
                        'to' => $followings->lastItem(),
                        'has_more_pages' => $followings->hasMorePages()
                    ]
                ]
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve followers',
                'error' => $e->getMessage()
            ], 500);
        }
    }



}