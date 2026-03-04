<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserGameProgress;
use App\Models\GameImage;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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

    /**
     * @OA\Post(
     *     path="/api/game-progress/images/upload",
     *     tags={"Game Progress"},
     *     summary="Upload image for game",
     *     description="Upload an image file from device to be used in the random image game",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="The image file to upload (JPEG, PNG, WebP, max 10MB)"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image uploaded successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image uploaded successfully"),
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
    public function uploadImage(Request $request)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240',
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation failed', ['errors' => $validator->errors()], 422);
            }

            $userId = Auth::id();
            $file = $request->file('image');

            if (!$file || !$file->isValid()) {
                return $this->sendError('Invalid image file provided', [], 400);
            }

            $folder = 'game-images/' . sprintf('%04d', (int)$userId / 1000) . '/' . $userId . '/' . date('Y/m/d');
            $extension = $file->getClientOriginalExtension();
            $newFileName = time() . '-' . Str::random(10) . '.' . $extension;

            $fullPath = public_path('uploads/' . $folder);
            if (!file_exists($fullPath)) {
                mkdir($fullPath, 0755, true);
            }

            $filePath = $file->storeAs($folder, $newFileName, 'uploads');
            $imageUrl = asset('uploads/' . $filePath);

            $gameImage = GameImage::create([
                'user_id' => $userId,
                'url' => $imageUrl,
            ]);

            return $this->sendSuccess($gameImage, 'Image uploaded successfully');

        } catch (Exception $exception) {
            Log::error("Error uploading game image: " . $exception->getMessage());
            return $this->sendError('Failed to upload image: ' . $exception->getMessage(), [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/game-progress/images",
     *     tags={"Game Progress"},
     *     summary="Get user's game images",
     *     description="Retrieve all images uploaded by the authenticated user for the game",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Images retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Images retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function getImages(Request $request)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $userId = Auth::id();
            $perPage = $request->input('per_page', 20);

            $images = GameImage::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->sendSuccess([
                'data' => $images->items(),
                'current_page' => $images->currentPage(),
                'last_page' => $images->lastPage(),
                'per_page' => $images->perPage(),
                'total' => $images->total(),
            ], 'Images retrieved successfully');

        } catch (Exception $exception) {
            Log::error("Error getting game images: " . $exception->getMessage());
            return $this->sendError('Failed to retrieve images', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/game-progress/images/{id}",
     *     tags={"Game Progress"},
     *     summary="Get single game image",
     *     description="Get a specific image by ID. User can only access their own images.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Image ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image retrieved successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image not found or access denied"
     *     )
     * )
     */
    public function getImage(Request $request, $id)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $userId = Auth::id();
            $image = GameImage::where('user_id', $userId)->where('id', $id)->first();

            if (!$image) {
                return $this->sendError('Image not found or you do not have permission to access this image', [], 404);
            }

            return $this->sendSuccess($image, 'Image retrieved successfully');

        } catch (Exception $exception) {
            Log::error("Error getting game image: " . $exception->getMessage());
            return $this->sendError('Failed to retrieve image', [], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/game-progress/images/{id}",
     *     tags={"Game Progress"},
     *     summary="Delete game image",
     *     description="Delete a specific image. User can only delete their own images.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Image ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Image deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Image deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Image not found or access denied"
     *     )
     * )
     */
    public function deleteImage(Request $request, $id)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $userId = Auth::id();
            $image = GameImage::where('user_id', $userId)->where('id', $id)->first();

            if (!$image) {
                return $this->sendError('Image not found or you do not have permission to access this image', [], 404);
            }

            if ($image->url) {
                $urlPath = parse_url($image->url, PHP_URL_PATH);
                $filePath = str_replace('/uploads/', '', $urlPath);
                $filePath = ltrim($filePath, '/');
                
                if ($filePath && Storage::disk('uploads')->exists($filePath)) {
                    Storage::disk('uploads')->delete($filePath);
                }
            }

            $image->delete();

            return $this->sendSuccess([], 'Image deleted successfully');

        } catch (Exception $exception) {
            Log::error("Error deleting game image: " . $exception->getMessage());
            return $this->sendError('Failed to delete image', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/game-progress/players",
     *     tags={"Game Progress"},
     *     summary="Get list of members who have played the game",
     *     description="Retrieve a paginated list of all members who have game progress (have played the game)",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=20)
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by member name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by: total_score, experience, current_level, total_play_time, created_at",
     *         required=false,
     *         @OA\Schema(type="string", default="total_score")
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order: asc or desc",
     *         required=false,
     *         @OA\Schema(type="string", default="desc")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Players retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Members who have played the game retrieved successfully"),
     *             @OA\Property(
     *                 property="players",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="first_name", type="string", example="John"),
     *                     @OA\Property(property="last_name", type="string", example="Doe"),
     *                     @OA\Property(property="user_name", type="string", example="johndoe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="bio", type="string", nullable=true, example="Game enthusiast"),
     *                     @OA\Property(property="avatar_url", type="string", example="https://example.com/avatars/1.jpg"),
     *                     @OA\Property(property="business_name", type="string", nullable=true, example=null),
     *                     @OA\Property(
     *                         property="game_progress",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="current_level", type="integer", example=5),
     *                         @OA\Property(property="current_stage", type="integer", nullable=true, example=3),
     *                         @OA\Property(property="current_checkpoint", type="integer", nullable=true, example=2),
     *                         @OA\Property(property="total_score", type="integer", example=15000),
     *                         @OA\Property(property="experience", type="integer", example=5000),
     *                         @OA\Property(property="coins", type="integer", example=250),
     *                         @OA\Property(property="lives", type="integer", example=5),
     *                         @OA\Property(property="total_play_time", type="integer", example=3600),
     *                         @OA\Property(property="completed_levels_count", type="integer", example=4),
     *                         @OA\Property(property="trophies_count", type="integer", example=2),
     *                         @OA\Property(property="last_played_at", type="string", format="date-time", example="2026-01-28T10:30:00.000000Z"),
     *                         @OA\Property(property="first_played_at", type="string", format="date-time", example="2026-01-25T08:15:00.000000Z")
     *                     )
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total", type="integer", example=50),
     *                 @OA\Property(property="last_page", type="integer", example=3),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=20),
     *                 @OA\Property(property="has_more_pages", type="boolean", example=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function players(Request $request)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $perPage = $request->input('per_page', 20);
            $perPage = min($perPage, 100); // Max 100 per page

            $sortBy = $request->input('sort_by', 'total_score');
            $sortOrder = $request->input('sort_order', 'desc');
            
            // Allowed sort fields
            $allowedSortFields = ['total_score', 'experience', 'current_level', 'total_play_time', 'created_at'];
            if (!in_array($sortBy, $allowedSortFields)) {
                $sortBy = 'total_score';
            }
            
            if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
                $sortOrder = 'desc';
            }

            // Query users who have game progress
            $query = UserGameProgress::with(['user' => function($q) {
                $q->where('status', 'publish')
                  ->where('role_id', '!=', 1);
            }])
            ->whereHas('user', function($q) {
                $q->where('status', 'publish')
                  ->where('role_id', '!=', 1);
            });

            // Search by member name
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->whereHas('user', function($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('user_name', 'like', '%' . $search . '%');
                });
            }

            // Sort - default by total_score descending (highest first)
            $query->orderBy($sortBy, $sortOrder);
            
            // Secondary sort for consistency when values are equal
            if ($sortBy !== 'created_at') {
                $query->orderBy('created_at', 'desc');
            } else {
                $query->orderBy('total_score', 'desc');
            }

            $progressList = $query->paginate($perPage);

            // Transform collection to include user and game progress data
            $players = collect($progressList->items())->map(function ($progress) {
                $user = $progress->user;
                if (!$user) {
                    return null;
                }

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'user_name' => $user->user_name,
                    'email' => $user->email,
                    'bio' => $user->bio,
                    'avatar_url' => $user->getAvatarUrl(),
                    'business_name' => $user->business_name,
                    'game_progress' => [
                        'id' => $progress->id,
                        'current_level' => $progress->current_level,
                        'current_stage' => $progress->current_stage,
                        'current_checkpoint' => $progress->current_checkpoint,
                        'total_score' => $progress->total_score,
                        'experience' => $progress->experience,
                        'coins' => $progress->coins,
                        'lives' => $progress->lives,
                        'total_play_time' => $progress->total_play_time,
                        'completed_levels_count' => ($progress->completed_levels_data && is_array($progress->completed_levels_data)) 
                            ? count($progress->completed_levels_data) 
                            : 0,
                        'trophies_count' => ($progress->trophies && is_array($progress->trophies)) 
                            ? count($progress->trophies) 
                            : 0,
                        'last_played_at' => $progress->updated_at,
                        'first_played_at' => $progress->created_at,
                    ]
                ];
            })->filter()->values(); // Remove null entries and reindex

            return $this->sendSuccess([
                'players' => $players,
                'pagination' => [
                    'current_page' => $progressList->currentPage(),
                    'per_page' => $progressList->perPage(),
                    'total' => $progressList->total(),
                    'last_page' => $progressList->lastPage(),
                    'from' => $progressList->firstItem(),
                    'to' => $progressList->lastItem(),
                    'has_more_pages' => $progressList->hasMorePages()
                ]
            ], 'Members who have played the game retrieved successfully');

        } catch (Exception $exception) {
            Log::error("Error getting members who played: " . $exception->getMessage());
            return $this->sendError('Failed to retrieve members who played', [], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/game-progress/user/{user_id}",
     *     tags={"Game Progress"},
     *     summary="Get game progress by user ID",
     *     description="Retrieve game progress details for a specific user by their user ID",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="User ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Game progress retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Game progress retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=123),
     *                 @OA\Property(property="current_level", type="integer", example=5),
     *                 @OA\Property(property="current_stage", type="integer", nullable=true, example=3),
     *                 @OA\Property(property="current_checkpoint", type="integer", nullable=true, example=2),
     *                 @OA\Property(property="total_score", type="integer", example=15000),
     *                 @OA\Property(property="experience", type="integer", example=5000),
     *                 @OA\Property(property="coins", type="integer", example=250),
     *                 @OA\Property(property="lives", type="integer", example=5),
     *                 @OA\Property(property="completed_levels_data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="trophies", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="total_play_time", type="integer", example=3600),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=123),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="user_name", type="string", example="johndoe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="avatar_url", type="string", example="https://example.com/avatars/123.jpg")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Game progress not found"
     *     )
     * )
     */
    public function getByUser(Request $request, $userId)
    {
        try {
            if (!Auth::check()) {
                return $this->sendError('Unauthorized', [], 401);
            }

            $userId = (int) $userId;
            
            // Get game progress with user relationship
            $progress = UserGameProgress::with(['user' => function($q) {
                $q->where('status', 'publish')
                  ->where('role_id', '!=', 1)
                  ->select('id', 'name', 'user_name', 'email', 'avatar_id', 'bio', 'first_name', 'last_name');
            }])
            ->whereHas('user', function($q) {
                $q->where('status', 'publish')
                  ->where('role_id', '!=', 1);
            })
            ->where('user_id', $userId)
            ->first();

            if (!$progress) {
                return $this->sendError('Game progress not found for this user', [], 404);
            }

            // Transform data to include user info
            $user = $progress->user;
            $progressData = $progress->toArray();
            
            if ($user) {
                $progressData['user'] = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'user_name' => $user->user_name,
                    'email' => $user->email,
                    'bio' => $user->bio,
                    'avatar_url' => $user->getAvatarUrl(),
                ];
            }

            return $this->sendSuccess($progressData, 'Game progress retrieved successfully');

        } catch (Exception $exception) {
            Log::error("Error getting game progress by user: " . $exception->getMessage());
            return $this->sendError('Failed to retrieve game progress', [], 500);
        }
    }
}
