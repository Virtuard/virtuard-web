<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Story;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    
    public function createStory(Request $request){
        try{
            $this->validate($request, [
                'media' => 'required|mimes:jpeg,png,mp4|max:20000',
            ]);

            $idUser = Auth::id();

            // Periksa apakah file media telah diunggah
            if ($request->hasFile('media')) {
                $mediaPath = $request->file('media')->store('/story');
            } else {
                $mediaPath = null;
            }

            $story = new Story;
            $story->user_id = $idUser;
            $story->link_text = $request->input('link_text');
            $story->link = $request->input('link');
            $story->media = $mediaPath;

            $story->save();
            
            

            return response()->json([
                'status' => 'success',
                'message' => 'Add story successfully',
                'data' => [
                    "user_id" => $idUser,
                    "urls" => [
                       [
                           "id" => $story->id,
                           "media" => $story->media,
                       ]
                    ],
                ],
            ]);
        }catch(\Exception $e) {
            Log::error("Error upload story:");
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function deleteStory(Request $request, $id)
    {
        try {
            // Validate the ID parameter
            if (!is_numeric($id) || $id <= 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid story ID'
                ], 400);
            }

            $storyId = (int) $id;
            $authenticatedUserId = auth()->id();
            

            // Find the story first to check ownership
            $story = Story::find($storyId);

            if (!$story) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Story not found'
                ], 404);
            }

            // Check if the authenticated user owns this story
            if ($story->user_id !== $authenticatedUserId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden: You can only delete your own stories'
                ], 403);
            }

            // Delete the story
            $story->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Story deleted successfully',
                'data' => [
                    'story_id' => $story->id,
                ]
            ], 200);

        }catch (\Exception $e) {
            Log::error("Error deleting story:");
            Log::error($e);

            return response()->json([
                "status" => "error",
                "message" => "Error deleting story"
            ], 500);
        }
    }
    
    public function getStories(Request $request) {
        try{
            // Get distinct users with pagination
            $users = DB::table('ref_story')
                ->select('user_id')
                ->distinct()
                ->orderByRaw('MAX(created_at) DESC')
                ->groupBy('user_id')
                ->paginate(10);
            
            $userIds = collect($users->items())->pluck('user_id');


            $stories = Story::whereIn('user_id', $userIds)
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy('user_id');

            $transformedItems = collect($users->items())->map(function ($user) use ($stories) {
                return [
                    'user_id' => $user->user_id,
                    'urls' => $stories->get($user->user_id, collect())->map(function ($story) {
                        return [
                            'id' => $story->id,
                            'media' => $story->media,
                        ];
                    })->toArray(),
                ];
            });

            $users->setCollection($transformedItems);

            return response()->json([
                "message" => "Get stories successfully",
                "status" => "success",
                "data" => $users,
            ]);
            
        }catch(\Exception $e) {
            Log::error("Error getting stories:");
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

}