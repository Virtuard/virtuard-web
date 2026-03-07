<?php

namespace Modules\Api\Controllers;

use Modules\ApiController;
use Modules\Core\Models\Attributes;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Attributes",
 *     description="API Endpoints for service attributes"
 * )
 */
class AttributeController extends ApiController
{
    protected $attributesClass;

    public function __construct(Attributes $attributes)
    {
        parent::__construct();
        $this->attributesClass = $attributes;
    }

    /**
     * @OA\Get(
     *     path="/api/attributes",
     *     tags={"Attributes"},
     *     summary="Get service attributes",
     *     description="Retrieve all available attributes for a specific service type with their associated terms",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="service",
     *         in="query",
     *         description="Service type to filter attributes (required)",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"hotel", "space", "business"},
     *             example="business"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Attributes fetched successfully"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="WiFi Internet"),
     *                     @OA\Property(property="slug", type="string", example="wifi-internet"),
     *                     @OA\Property(property="service", type="string", example="business"),
     *                     @OA\Property(property="display_type", type="string", example="checkbox"),
     *                     @OA\Property(property="icon", type="string", example="icofont-wifi"),
     *                     @OA\Property(property="hide_in_single", type="boolean", example=false),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-07-18T09:03:18.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-05T12:33:42.000000Z"),
     *                     @OA\Property(
     *                         property="terms",
     *                         type="array",
     *                         description="Associated terms for this attribute",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Free WiFi"),
     *                             @OA\Property(property="slug", type="string", example="free-wifi"),
     *                             @OA\Property(property="content", type="string", example="High-speed internet access"),
     *                             @OA\Property(property="image_id", type="integer", example=10),
     *                             @OA\Property(property="icon", type="string", example="fa fa-wifi"),
     *                             @OA\Property(property="created_at", type="string", format="date-time"),
     *                             @OA\Property(property="updated_at", type="string", format="date-time")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request - Service parameter is required",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Service is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Something went wrong")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $service = $request->query('service');

            if (empty($service)) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Service is required',
                ], 400);
            }

            $rows = $this->attributesClass::with('terms')->where('service', $service)->get();

            return response()->json([
                'status' => 1,
                'message' => 'Attributes fetched successfully',
                'data' => $rows
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
