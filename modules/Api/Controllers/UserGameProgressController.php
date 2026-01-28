<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserGameProgress;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class UserGameProgressController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/game-progress",
     *     tags={"Game Progress"},
     *     summary="Get current user's game progress",
     *     description="Retrieve the authenticated user's game progress",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $userId = Auth::id();
            $progress = UserGameProgress::where('user_id', $userId)->first();

            if (!$progress) {
                // Create default progress if not exists
                $progress = UserGameProgress::create([
                    'user_id' => $userId,
                    'current_level' => 1,
                    'current_stage' => null,
                    'current_checkpoint' => null,
                    'total_score' => 0,
                    'experience' => 0,
                    'coins' => 0,
                    'lives' => 5,
                    'completed_levels_data' => [],
                    'trophies' => [],
                    'total_play_time' => 0,
                ]);
            }

            return $this->sendSuccess($progress, 'Game progress retrieved successfully');

        } catch (Exception $exception) {
            Log::error("Error getting game progress: " . $exception->getMessage());
            return $this->sendError('Failed to retrieve game progress', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/game-progress",
     *     tags={"Game Progress"},
     *     summary="Save game progress (Create or Update)",
     *     description="Create new game progress if not exists, or update existing game progress. This endpoint automatically handles both create and update operations.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="current_level", type="integer", example=1),
     *             @OA\Property(property="current_stage", type="integer", example=1),
     *             @OA\Property(property="current_checkpoint", type="integer", example=1),
     *             @OA\Property(property="total_score", type="integer", example=1000),
     *             @OA\Property(property="experience", type="integer", example=500),
     *             @OA\Property(property="coins", type="integer", example=100),
     *             @OA\Property(property="lives", type="integer", example=5),
     *             @OA\Property(property="completed_levels_data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="trophies", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="total_play_time", type="integer", example=3600)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game progress saved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Game progress saved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $validator = Validator::make($request->all(), [
                'current_level' => 'nullable|integer|min:1',
                'current_stage' => 'nullable|integer|min:0',
                'current_checkpoint' => 'nullable|integer|min:0',
                'total_score' => 'nullable|integer|min:0',
                'experience' => 'nullable|integer|min:0',
                'coins' => 'nullable|integer|min:0',
                'lives' => 'nullable|integer|min:0|max:10',
                'completed_levels_data' => 'nullable|array',
                'trophies' => 'nullable|array',
                'total_play_time' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation failed', ['errors' => $validator->errors()], 422);
            }

            $userId = Auth::id();
            $user = Auth::user();

            // Check if progress exists
            $progress = UserGameProgress::where('user_id', $userId)->first();

            if ($progress) {
                // Update existing progress
                $progress->update($request->only([
                    'current_level',
                    'current_stage',
                    'current_checkpoint',
                    'total_score',
                    'experience',
                    'coins',
                    'lives',
                    'completed_levels_data',
                    'trophies',
                    'total_play_time',
                ]));
            } else {
                // Create new progress
                $progress = UserGameProgress::create(array_merge(
                    $request->only([
                        'current_level',
                        'current_stage',
                        'current_checkpoint',
                        'total_score',
                        'experience',
                        'coins',
                        'lives',
                        'completed_levels_data',
                        'trophies',
                        'total_play_time',
                    ]),
                    [
                        'user_id' => $userId,
                        'current_level' => $request->current_level ?? 1,
                        'total_score' => $request->total_score ?? 0,
                        'experience' => $request->experience ?? 0,
                        'coins' => $request->coins ?? 0,
                        'lives' => $request->lives ?? 5,
                        'completed_levels_data' => $request->completed_levels_data ?? [],
                        'trophies' => $request->trophies ?? [],
                        'total_play_time' => $request->total_play_time ?? 0,
                    ]
                ));
            }

            return $this->sendSuccess($progress, 'Game progress saved successfully');

        } catch (Exception $exception) {
            Log::error("Error saving game progress: " . $exception->getMessage());
            return $this->sendError('Failed to save game progress', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/game-progress/add-score",
     *     tags={"Game Progress"},
     *     summary="Add score to game progress",
     *     description="Add score, experience, and coins to the user's game progress",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="score", type="integer", example=100),
     *             @OA\Property(property="experience", type="integer", example=50),
     *             @OA\Property(property="coins", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Score added successfully"
     *     )
     * )
     */
    public function addScore(Request $request)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $validator = Validator::make($request->all(), [
                'score' => 'required|integer|min:0',
                'experience' => 'nullable|integer|min:0',
                'coins' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation failed', ['errors' => $validator->errors()], 422);
            }

            $userId = Auth::id();
            $progress = UserGameProgress::where('user_id', $userId)->first();

            if (!$progress) {
                // Create default progress if not exists
                $progress = UserGameProgress::create([
                    'user_id' => $userId,
                    'current_level' => 1,
                    'total_score' => $request->score,
                    'experience' => $request->experience ?? 0,
                    'coins' => $request->coins ?? 0,
                    'lives' => 5,
                    'completed_levels_data' => [],
                    'trophies' => [],
                    'total_play_time' => 0,
                ]);
            } else {
                $progress->total_score += $request->score;
                if ($request->has('experience')) {
                    $progress->experience += $request->experience;
                }
                if ($request->has('coins')) {
                    $progress->coins += $request->coins;
                }
                $progress->save();
            }

            return $this->sendSuccess($progress, 'Score added successfully');

        } catch (Exception $exception) {
            Log::error("Error adding score: " . $exception->getMessage());
            return $this->sendError('Failed to add score', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/game-progress/use-life",
     *     tags={"Game Progress"},
     *     summary="Use a life",
     *     description="Decrease user's life count by 1",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Life used successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No lives available"
     *     )
     * )
     */
    public function useLife(Request $request)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $userId = Auth::id();
            $progress = UserGameProgress::where('user_id', $userId)->first();

            if (!$progress) {
                return $this->sendError('Game progress not found', [], 404);
            }

            if ($progress->lives <= 0) {
                return $this->sendError('No lives available', [], 400);
            }

            $progress->lives -= 1;
            $progress->save();

            return $this->sendSuccess($progress, 'Life used successfully');

        } catch (Exception $exception) {
            Log::error("Error using life: " . $exception->getMessage());
            return $this->sendError('Failed to use life', [], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/game-progress/add-play-time",
     *     tags={"Game Progress"},
     *     summary="Add play time",
     *     description="Add play time in seconds to the user's total play time",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="seconds", type="integer", example=60)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Play time added successfully"
     *     )
     * )
     */
    public function addPlayTime(Request $request)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $validator = Validator::make($request->all(), [
                'seconds' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation failed', ['errors' => $validator->errors()], 422);
            }

            $userId = Auth::id();
            $progress = UserGameProgress::where('user_id', $userId)->first();

            if (!$progress) {
                // Create default progress if not exists
                $progress = UserGameProgress::create([
                    'user_id' => $userId,
                    'current_level' => 1,
                    'total_score' => 0,
                    'experience' => 0,
                    'coins' => 0,
                    'lives' => 5,
                    'completed_levels_data' => [],
                    'trophies' => [],
                    'total_play_time' => $request->seconds,
                ]);
            } else {
                $progress->total_play_time += $request->seconds;
                $progress->save();
            }

            return $this->sendSuccess($progress, 'Play time added successfully');

        } catch (Exception $exception) {
            Log::error("Error adding play time: " . $exception->getMessage());
            return $this->sendError('Failed to add play time', [], 500);
        }
    }
}
