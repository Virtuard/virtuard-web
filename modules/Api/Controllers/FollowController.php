<?php

namespace Modules\Api\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            // Query to get followers with JOIN
            $followers = DB::table('follow_member')
                ->join('users', 'follow_member.follower_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.user_name',
                    'users.avatar_id'
                )
                ->where('follow_member.user_id', auth()->user()->id)
                ->orderBy('follow_member.created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Add avatar_url to each follower
            $followers->getCollection()->transform(function ($user) {
                $user->avatar_url = get_file_url($user->avatar_id, 'full');
                // Remove avatar_id from response as it's not needed
                unset($user->avatar_id);
                return $user;
            });

            return response()->json([
                'success' => true,
                'message' => 'Followers retrieved successfully',
                'data' => [
                    'followers' => $followers->items(),
                    'pagination' => [
                        'current_page' => $followers->currentPage(),
                        'per_page' => $followers->perPage(),
                        'total' => $followers->total(),
                        'last_page' => $followers->lastPage(),
                        'from' => $followers->firstItem(),
                        'to' => $followers->lastItem(),
                        'has_more_pages' => $followers->hasMorePages()
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

            // Query to get followers with JOIN
            $followers = DB::table('follow_member')
                ->join('users', 'follow_member.follower_id', '=', 'users.id')
                ->select(
                    'users.id',
                    'users.name',
                    'users.user_name',
                    'users.avatar_id'
                )
                ->where('follow_member.follower_id', auth()->user()->id)
                ->orderBy('follow_member.created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            // Add avatar_url to each follower
            $followers->getCollection()->transform(function ($user) {
                $user->avatar_url = get_file_url($user->avatar_id, 'full');
                // Remove avatar_id from response as it's not needed
                unset($user->avatar_id);
                return $user;
            });

            return response()->json([
                'success' => true,
                'message' => 'Followings retrieved successfully',
                'data' => [
                    'followers' => $followers->items(),
                    'pagination' => [
                        'current_page' => $followers->currentPage(),
                        'per_page' => $followers->perPage(),
                        'total' => $followers->total(),
                        'last_page' => $followers->lastPage(),
                        'from' => $followers->firstItem(),
                        'to' => $followers->lastItem(),
                        'has_more_pages' => $followers->hasMorePages()
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