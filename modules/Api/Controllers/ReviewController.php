<?php
namespace Modules\Api\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReviewController extends \Modules\Review\Controllers\ReviewController
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * @OA\Post(
     *     path="/api/{type}/write-review/{id}",
     *     tags={"Review"},
     *     summary="Submit a review and rating",
     *     description="Submit a review and rating for a service. Requires authentication.",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Service type (hotel, space, business, etc.)",
     *         @OA\Schema(type="string", example="hotel")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"review_title", "review_content"},
     *             @OA\Property(property="review_title", type="string", example="Great experience!"),
     *             @OA\Property(property="review_content", type="string", example="I had a wonderful stay at this hotel. The service was excellent and the room was very clean."),
     *             @OA\Property(property="review_rate", type="number", format="float", example=4.5, description="Rating from 0 to 5 (required if review_stats is not provided)"),
     *             @OA\Property(property="review_stats", type="object", description="Detailed rating stats (optional, if service has review_stats configured)", 
     *                 @OA\Property(property="Cleanliness", type="number", example=5),
     *                 @OA\Property(property="Service", type="number", example=4),
     *                 @OA\Property(property="Location", type="number", example=5)
     *             ),
     *             @OA\Property(property="review_upload", type="array", description="Array of image IDs to upload with review (optional)",
     *                 @OA\Items(type="integer", example=123)
     *             ),
     *             @OA\Property(property="review_id", type="integer", description="Review ID if updating existing review (optional)", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Review submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="message", type="string", example="Review success!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error or business logic error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Review Title is required field")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="You have to login in to do this")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Service not found")
     *         )
     *     )
     * )
     */
    public function writeReview(Request $request, $type = '', $id = '')
    {
        if (!Auth::check()) {
            return $this->sendError(__("You have to login in to do this"))->setStatusCode(401);
        }

        // Validate type and id
        if (empty($type) || empty($id)) {
            return $this->sendError(__("Service type and ID are required"))->setStatusCode(400);
        }

        // Validate id is numeric
        if (!is_numeric($id)) {
            return $this->sendError(__("Service ID must be a number"))->setStatusCode(400);
        }

        $request->merge(['review_service_type' => $type]);
        $request->merge(['review_service_id' => $id]);
        
        return parent::addReview($request, true);
    }
}
