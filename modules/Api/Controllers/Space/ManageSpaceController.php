<?php

namespace Modules\Api\Controllers\Space;

use Modules\Core\Events\CreatedServicesEvent;
use Modules\Core\Events\UpdatedServiceEvent;
use Modules\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Space\Hook;
use Modules\Space\Models\Space;
use Modules\Location\Models\Location;
use Modules\Core\Models\Attributes;
use Modules\Booking\Models\Booking;
use Modules\Space\Models\SpaceCategory;
use Modules\Space\Models\SpaceTerm;
use Modules\Space\Models\SpaceTranslation;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Manage Space",
 *     description="API Endpoints for Space management"
 * )
 */
class ManageSpaceController extends ApiController
{
    protected $spaceClass;
    protected $spaceTranslationClass;
    protected $spaceTermClass;
    protected $attributesClass;
    protected $locationClass;
    protected $bookingClass;
    protected $spaceCategoryClass;
    /**
     * @var string
     */

    public function __construct(Space $space, SpaceTranslation $spaceTrans)
    {
        parent::__construct();
        $this->spaceClass = $space;
        $this->spaceTranslationClass = $spaceTrans;
        $this->spaceTermClass = SpaceTerm::class;
        $this->attributesClass = Attributes::class;
        $this->locationClass = Location::class;
        $this->bookingClass = Booking::class;
        $this->spaceCategoryClass = SpaceCategory::class;
    }

    public function callAction($method, $parameters)
    {
        if (!Space::isEnable()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Space service is not available'
            ], 503);
        }
        return parent::callAction($method, $parameters);
    }

    /**
     * @OA\Get(
     *     path="/api/user/space",
     *     tags={"Manage Space"},
     *     summary="Get user's spaces",
     *     description="Retrieve a paginated list of spaces owned by the authenticated user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", 
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Space Stanford"),
     *                         @OA\Property(property="slug", type="string", example="space-stanford"),
     *                         @OA\Property(property="price", type="number", format="float", example=300.00),
     *                         @OA\Property(property="star_rate", type="integer", example=5),
     *                         @OA\Property(property="status", type="string", example="publish"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-07-18 09:03:18")
     *                     )
     *                 ),
     *                 @OA\Property(property="first_page_url", type="string"),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=1),
     *                 @OA\Property(property="last_page_url", type="string"),
     *                 @OA\Property(property="next_page_url", type="string", nullable=true),
     *                 @OA\Property(property="path", type="string"),
     *                 @OA\Property(property="per_page", type="integer", example=5),
     *                 @OA\Property(property="prev_page_url", type="string", nullable=true),
     *                 @OA\Property(property="to", type="integer", example=3),
     *                 @OA\Property(property="total", type="integer", example=3)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Service unavailable - Space service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Space service is not available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="something went wrong")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $user_id = Auth::id();
            $list_space = $this->spaceClass::where('author_id', $user_id)->orderBy('id', 'desc');
            $rows = $list_space->paginate(5);

            $data = [
                'status' => 'success',
                'message' => 'success',
                'data' => $rows,
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['message' => 'something went wrong'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/space",
     *     tags={"Manage Space"},
     *     summary="Create a new space",
     *     description="Create a new space. Requires space_create permission.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Space Stanford", description="Space title"),
     *             @OA\Property(property="image_id", type="integer", example=341, description="Featured image ID"),
     *             @OA\Property(property="banner_image_id", type="integer", example=93, description="Banner image ID"),
     *             @OA\Property(property="gallery", type="array", @OA\Items(type="integer"), example={97,98,99,100,101,102}, description="Gallery image IDs"),
     *             @OA\Property(property="is_featured", type="boolean", example=false, description="Is featured space"),
     *             @OA\Property(
     *                 property="policy",
     *                 type="string", 
     *                 example="[{'title':'Guarantee Policy','content':''}]",
     *                 description="Space policies"
     *             ),
     *             @OA\Property(property="location_id", type="integer", example=1, description="Location ID"),
     *             @OA\Property(property="address", type="string", example="Arrondissement de Paris", description="Space address"),
     *             @OA\Property(property="map_lat", type="number", format="float", example=19.148665, description="Latitude"),
     *             @OA\Property(property="map_lng", type="number", format="float", example=72.839670, description="Longitude"),
     *             @OA\Property(property="map_zoom", type="integer", example=12, description="Map zoom level"),
     *             @OA\Property(property="star_rate", type="integer", example=5, description="Star rating (1-5)"),
     *             @OA\Property(property="price", type="number", format="float", example=300.00, description="Base price"),
     *             @OA\Property(property="sale_price", type="number", format="float", example=null, description="Sale price"),
     *             @OA\Property(property="check_in_time", type="string", example="12:00AM", description="Check-in time"),
     *             @OA\Property(property="check_out_time", type="string", example="11:00AM", description="Check-out time"),
     *             @OA\Property(property="allow_full_day", type="boolean", example=false, description="Allow full day booking"),
     *             @OA\Property(property="enable_extra_price", type="boolean", example=true, description="Enable extra pricing"),
     *             @OA\Property(property="extra_price", type="string", example="[{'name':'Service VIP','price':'200','type':'one_time'},{'name':'Breakfasts','price':'100','type':'one_time'}]"),
     *             @OA\Property(property="min_day_before_booking", type="integer", example=null, description="Minimum days before booking"),
     *             @OA\Property(property="min_day_stays", type="integer", example=null, description="Minimum days to stay"),
     *             @OA\Property(property="enable_service_fee", type="boolean", example=false, description="Enable service fee"),
     *             @OA\Property(property="service_fee", type="string", example=null),
     *             @OA\Property(property="surrounding", type="string", example=null),
     *             @OA\Property(property="category_id", type="integer", example=1, description="Space category ID"),
     *             @OA\Property(property="room", type="integer", example=null, description="Number of rooms"),
     *             @OA\Property(property="chain", type="string", example=null, description="Space chain name"),
     *             @OA\Property(property="phone", type="string", example=null, description="Space phone number"),
     *             @OA\Property(property="website", type="string", example=null, description="Space website URL"),
     *             @OA\Property(property="ipanorama_id", type="integer", example=1, description="Panorama ID"),
     *             @OA\Property(property="terms", type="array", @OA\Items(type="integer"), example={1,2,3}, description="Term IDs"),
     *             @OA\Property(property="lang", type="string", example="en", description="Language code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Space created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Space created"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Space Stanford"),
     *                 @OA\Property(property="slug", type="string", example="space-stanford"),
     *                 @OA\Property(property="content", type="string", example="<p>Built in 1986, Space Stanford is a distinct addition to New York (NY) and a smart choice for travelers.</p>"),
     *                 @OA\Property(property="price", type="number", format="float", example=300.00),
     *                 @OA\Property(property="star_rate", type="integer", example=5),
     *                 @OA\Property(property="status", type="string", example="publish")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string", example="The title field is required."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Insufficient permissions",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You do not have permission to create spaces")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Service unavailable - Space service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Space service is not available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something went wrong")
     *         )
     *     )
     * )
     */
    public function store(Request $request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            if ($id > 0) {
                $this->checkPermission('space_update');
                $row = $this->spaceClass::find($id);
                if (empty($row)) {
                    return response()->json(['message' => __('Space not found')], 404);
                }

                if ($row->author_id != Auth::id() and !$this->hasPermission('space_manage_others')) {
                    return response()->json(['message' => __('You are not authorized to update this space')], 403);
                }
            } else {
                $this->checkPermission('space_create');
                $row = new $this->spaceClass();
                $row->status = 'publish';
                if (setting_item('space_vendor_create_service_must_approved_by_admin', 0)) {
                    $row->status = 'pending';
                }
                $row->author_id = Auth::id();
            }
            $dataKeys = [
                'title',
                'content',
                'slug',
                'video',
                'image_id',
                'banner_image_id',
                'gallery',
                'is_featured',
                'policy',
                'location_id',
                'address',
                'map_lat',
                'map_lng',
                'map_zoom',
                'star_rate',
                'price',
                'sale_price',
                'check_in_time',
                'check_out_time',
                'allow_full_day',
                'enable_extra_price',
                'extra_price',
                'min_day_before_booking',
                'min_day_stays',
                'enable_service_fee',
                'service_fee',
                'surrounding',
                'category_id',
                'room',
                'chain',
                'phone',
                'website',
                'ipanorama_id',
            ];

            $row->fillByAttr($dataKeys, $request->input());

            $row['image_id'] = resize_feature_image($row->image_id);

            $res = $row->saveOriginOrTranslation($request->input('lang'), true);

            if ($res) {
                if (!$request->input('lang') or is_default_lang($request->input('lang'))) {
                    $this->saveTerms($row, $request);
                }
                do_action(Hook::AFTER_SAVING, $row, $request);

                if ($id > 0) {
                    event(new UpdatedServiceEvent($row));
                    return response()->json([
                        'status' => 'success',
                        'message' => __('Space updated'),
                        'data' => $row
                    ]);
                } else {
                    event(new CreatedServicesEvent($row));
                    return response()->json([
                        'status' => 'success',
                        'message' => __('Space created'),
                        'data' => $row
                    ]);
                }
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something went wrong'
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/user/space/{id}",
     *     tags={"Manage Space"},
     *     summary="Update an existing space",
     *     description="Update an existing space by ID. Requires space_update permission and ownership or space_manage_others permission.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Space ID to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Updated Grand Space", description="Space title"),
     *             @OA\Property(property="content", type="string", example="Updated luxury space description", description="Space description"),
     *             @OA\Property(property="slug", type="string", example="updated-grand-space", description="URL slug"),
     *             @OA\Property(property="image_id", type="integer", example=1, description="Featured image ID"),
     *             @OA\Property(property="banner_image_id", type="integer", example=2, description="Banner image ID"),
     *             @OA\Property(property="gallery", type="array", @OA\Items(type="integer"), example={3,4,5}, description="Gallery image IDs"),
     *             @OA\Property(property="is_featured", type="boolean", example=true, description="Is featured space"),
     *             @OA\Property(property="policy", type="string", example="Space policies", description="Space policies"),
     *             @OA\Property(property="location_id", type="integer", example=1, description="Location ID"),
     *             @OA\Property(property="address", type="string", example="123 Main St, City", description="Space address"),
     *             @OA\Property(property="map_lat", type="number", format="float", example=40.7128, description="Latitude"),
     *             @OA\Property(property="map_lng", type="number", format="float", example=-74.0060, description="Longitude"),
     *             @OA\Property(property="map_zoom", type="integer", example=15, description="Map zoom level"),
     *             @OA\Property(property="star_rate", type="integer", example=4, description="Star rating (1-5)"),
     *             @OA\Property(property="price", type="number", format="float", example=150.00, description="Base price"),
     *             @OA\Property(property="sale_price", type="number", format="float", example=120.00, description="Sale price"),
     *             @OA\Property(property="check_in_time", type="string", example="15:00", description="Check-in time"),
     *             @OA\Property(property="check_out_time", type="string", example="11:00", description="Check-out time"),
     *             @OA\Property(property="allow_full_day", type="boolean", example=true, description="Allow full day booking"),
     *             @OA\Property(property="enable_extra_price", type="boolean", example=true, description="Enable extra pricing"),
     *             @OA\Property(property="extra_price", type="string"),
     *             @OA\Property(property="min_day_before_booking", type="integer", example=1, description="Minimum days before booking"),
     *             @OA\Property(property="min_day_stays", type="integer", example=1, description="Minimum days to stay"),
     *             @OA\Property(property="enable_service_fee", type="boolean", example=true, description="Enable service fee"),
     *             @OA\Property(property="service_fee", type="string"),
     *             @OA\Property(property="surrounding", type="string"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="Space category ID"),
     *             @OA\Property(property="room", type="integer", example=50, description="Number of rooms"),
     *             @OA\Property(property="chain", type="string", example="Marriott", description="Space chain name"),
     *             @OA\Property(property="phone", type="string", example="+1-555-123-4567", description="Space phone number"),
     *             @OA\Property(property="website", type="string", example="https://site.com", description="Space website URL"),
     *             @OA\Property(property="ipanorama_id", type="integer", example=1, description="Panorama ID"),
     *             @OA\Property(property="terms", type="array", @OA\Items(type="integer"), example={1,2,3}, description="Term IDs"),
     *             @OA\Property(property="lang", type="string", example="en", description="Language code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Space updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Space updated"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Space Stanford"),
     *                 @OA\Property(property="slug", type="string", example="space-stanford"),
     *                 @OA\Property(property="status", type="string", example="publish"),
     *                 @OA\Property(property="author_id", type="integer", example=1),
     *                 @OA\Property(property="price", type="number", format="float", example=300.00),
     *                 @OA\Property(property="star_rate", type="integer", example=5),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-05 12:33:42")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Space not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Space not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Insufficient permissions or not owner",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to update this space")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="title", type="array", @OA\Items(type="string", example="The title field is required."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Service unavailable - Space service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Space service is not available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something went wrong")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        return $this->store($request, $id);
    }

    /**
     * @OA\Get(
     *     path="/api/user/space/{id}",
     *     tags={"Manage Space"},
     *     summary="Get space details",
     *     description="Retrieve detailed information about a specific space",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Space ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Space Stanford"),
     *                 @OA\Property(property="slug", type="string", example="space-stanford"),
     *                 @OA\Property(property="content", type="string", example="<p>Built in 1986, Space Stanford is a distinct addition to New York (NY) and a smart choice for travelers.</p>"),
     *                 @OA\Property(property="price", type="number", format="float", example=300.00),
     *                 @OA\Property(property="star_rate", type="integer", example=5),
     *                 @OA\Property(property="address", type="string", example="Arrondissement de Paris"),
     *                 @OA\Property(property="map_lat", type="number", format="float", example=19.148665),
     *                 @OA\Property(property="map_lng", type="number", format="float", example=72.839670),
     *                 @OA\Property(property="status", type="string", example="publish"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-07-18 09:03:18"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-05 12:33:42")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Space not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Space not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Service unavailable - Space service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Space service is not available")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $space = $this->spaceClass::find($id);
            
            if (empty($space)) {
                return response()->json(['message' => __('Space not found')], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $space
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'something went wrong'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/space/{id}",
     *     tags={"Manage Space"},
     *     summary="Delete a space",
     *     description="Delete a space by ID. Requires space_delete permission and ownership of the space.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Space ID to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="permanently_delete",
     *         in="query",
     *         required=false,
     *         description="Permanently delete the space (soft delete by default)",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Space deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Delete space success!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Space not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Space not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Insufficient permissions",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You do not have permission to delete spaces")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - User not authenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="Service unavailable - Space service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Space service is not available")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="something went wrong")
     *         )
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $this->checkPermission('space_delete');
            $user_id = Auth::id();
            if (\request()->query('permanently_delete')) {
                $query = $this->spaceClass
                    ::where('author_id', $user_id)
                    ->where('id', $id)
                    ->withTrashed()
                    ->first();
                if (!empty($query)) {
                    $query->forceDelete();
                }
            } else {
                $query = $this->spaceClass
                    ::where('author_id', $user_id)
                    ->where('id', $id)
                    ->first();
                if (!empty($query)) {
                    $query->delete();
                    event(new UpdatedServiceEvent($query));
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => __('Delete space success!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something went wrong'
            ], 500);
        }
    }

    /**
     * Save space terms
     * 
     * @param Space $row
     * @param Request $request
     * @return void
     */
    public function saveTerms($row, $request)
    {
        if (empty($request->input('terms'))) {
            $this->spaceTermClass::where('target_id', $row->id)->delete();
        } else {
            $term_ids = $request->input('terms');
            foreach ($term_ids as $term_id) {
                $this->spaceTermClass::firstOrCreate([
                    'term_id' => $term_id,
                    'target_id' => $row->id,
                ]);
            }
            $this->spaceTermClass
                ::where('target_id', $row->id)
                ->whereNotIn('term_id', $term_ids)
                ->delete();
        }
    }
}
