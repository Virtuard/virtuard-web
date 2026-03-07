<?php

namespace Modules\Api\Controllers\Business;

use Modules\Core\Events\CreatedServicesEvent;
use Modules\Core\Events\UpdatedServiceEvent;
use Modules\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Business\Hook;
use Modules\Business\Models\Business;
use Modules\Location\Models\Location;
use Modules\Core\Models\Attributes;
use Modules\Booking\Models\Booking;
use Modules\Business\Models\BusinessCategory;
use Modules\Business\Models\BusinessTerm;
use Modules\Business\Models\BusinessTranslation;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Manage Business",
 *     description="API Endpoints for Business management"
 * )
 */
class ManageBusinessController extends ApiController
{
    protected $businessClass;
    protected $businessTranslationClass;
    protected $businessTermClass;
    protected $attributesClass;
    protected $locationClass;
    protected $bookingClass;
    protected $businessCategoryClass;
    /**
     * @var string
     */

    public function __construct(Business $business, BusinessTranslation $businessTrans)
    {
        parent::__construct();
        $this->businessClass = $business;
        $this->businessTranslationClass = $businessTrans;
        $this->businessTermClass = BusinessTerm::class;
        $this->attributesClass = Attributes::class;
        $this->locationClass = Location::class;
        $this->bookingClass = Booking::class;
        $this->businessCategoryClass = BusinessCategory::class;
    }

    public function callAction($method, $parameters)
    {
        if (!Business::isEnable()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Business service is not available'
            ], 503);
        }
        return parent::callAction($method, $parameters);
    }

    /**
     * @OA\Get(
     *     path="/api/user/business",
     *     tags={"Manage Business"},
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
     *                         @OA\Property(property="title", type="string", example="Business Stanford"),
     *                         @OA\Property(property="slug", type="string", example="business-stanford"),
     *                         @OA\Property(property="price", type="number", format="float", example=300.00),

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
     *         description="Service unavailable - Business service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Business service is not available")
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
            $list_business = $this->businessClass::where('author_id', $user_id)->orderBy('id', 'desc');
            $rows = $list_business->paginate(5);

            $data = [
                'status' => 1,
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
     *     path="/api/user/business",
     *     tags={"Manage Business"},
     *     summary="Create a new business",
     *     description="Create a new business. Requires business_create permission.",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Business Stanford", description="Business title"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="business-stanford", description="URL slug"),
     *             @OA\Property(property="content", type="string", example="Premium business services in the heart of the city", description="Business description"),
     *             @OA\Property(property="image_id", type="integer", example=341, description="Featured image ID"),
     *             @OA\Property(property="banner_image_id", type="integer", example=93, description="Banner image ID"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="Business category ID"),
     *             @OA\Property(property="location_id", type="integer", example=1, description="Location ID"),
     *             @OA\Property(property="address", type="string", example="123 Business District, City", description="Business address"),
     *             @OA\Property(property="map_lat", type="string", example="40.7128", description="Latitude"),
     *             @OA\Property(property="map_lng", type="string", example="-74.0060", description="Longitude"),
     *             @OA\Property(property="map_zoom", type="integer", example=15, description="Map zoom level"),
     *             @OA\Property(property="is_featured", type="boolean", example=false, description="Is featured business"),
     *             @OA\Property(property="gallery", type="string", maxLength=255, example="97,98,99", description="Gallery image IDs (comma-separated)"),
     *             @OA\Property(property="video", type="string", maxLength=255, example="business_video_url", description="Video URL"),
     *             @OA\Property(property="faqs", type="string", example="[{'question':'Business FAQ','answer':'FAQ answer'}]", description="Frequently asked questions"),
     *             @OA\Property(property="price", type="number", format="decimal", example=300.00, description="Base price"),
     *             @OA\Property(property="sale_price", type="number", format="decimal", example=250.00, description="Sale price"),
     *             @OA\Property(property="is_instant", type="boolean", example=false, description="Is instant booking enabled"),
     *             @OA\Property(property="allow_children", type="boolean", example=true, description="Allow children"),
     *             @OA\Property(property="allow_infant", type="boolean", example=true, description="Allow infants"),
     *             @OA\Property(property="max_guests", type="integer", example=20, description="Maximum number of guests"),
     *             @OA\Property(property="bed", type="integer", example=0, description="Number of beds (if applicable)"),
     *             @OA\Property(property="bathroom", type="integer", example=2, description="Number of bathrooms"),
     *             @OA\Property(property="square", type="integer", example=500, description="Square meters/feet"),
     *             @OA\Property(property="enable_extra_price", type="boolean", example=true, description="Enable extra pricing"),
     *             @OA\Property(property="extra_price", type="string", example="[{'name':'Premium Service','price':'100','type':'one_time'}]", description="Extra price configuration"),
     *             @OA\Property(property="discount_by_days", type="string", example="[{'day_from':7,'discount':10}]", description="Discount by days configuration"),
     *             @OA\Property(property="default_state", type="boolean", example=true, description="Default state"),
     *             @OA\Property(property="ical_import_url", type="string", maxLength=191, example="https://business.com/calendar.ics", description="iCal import URL"),
     *             @OA\Property(property="enable_service_fee", type="boolean", example=false, description="Enable service fee"),
     *             @OA\Property(property="service_fee", type="string", example="[{'name':'Service Fee','price':'25'}]", description="Service fee configuration"),
     *             @OA\Property(property="surrounding", type="string", example="Located in prime business district with easy access", description="Surrounding area description"),
     *             @OA\Property(property="min_day_before_booking", type="integer", example=1, description="Minimum days before booking"),
     *             @OA\Property(property="min_day_stays", type="integer", example=1, description="Minimum days to stay"),
     *             @OA\Property(property="franchising", type="string", maxLength=191, example="Business Franchise Network", description="Franchise information"),
     *             @OA\Property(property="phone", type="string", maxLength=191, example="+1-555-123-4567", description="Business phone number"),
     *             @OA\Property(property="website", type="string", maxLength=191, example="https://business.com", description="Business website URL"),
     *             @OA\Property(property="ipanorama_id", type="integer", example=1, description="iPanorama ID"),
     *             @OA\Property(property="list_product", type="string", example="[{'name':'Product A','price':'50'},{'name':'Product B','price':'75'}]", description="Product listing"),
     *             @OA\Property(property="items", type="string", example="[{'name':'Service A','description':'Premium service'},{'name':'Service B','description':'Basic service'}]", description="Items/services offered"),
     *             @OA\Property(property="terms", type="array", @OA\Items(type="integer"), example={1,2,3}, description="Term IDs"),
     *             @OA\Property(property="lang", type="string", example="en", description="Language code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Business created"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Business Stanford"),
     *                 @OA\Property(property="slug", type="string", example="business-stanford"),
     *                 @OA\Property(property="price", type="number", format="decimal", example=300.00),
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
     *             @OA\Property(property="message", type="string", example="You do not have permission to create businesses")
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
     *         description="Service unavailable - Business service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Business service is not available")
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
                $this->checkPermission('business_update');
                $row = $this->businessClass::find($id);
                if (empty($row)) {
                    return response()->json(['message' => __('Business not found')], 404);
                }

                if ($row->author_id != Auth::id() and !$this->hasPermission('business_manage_others')) {
                    return response()->json(['message' => __('You are not authorized to update this business')], 403);
                }
            } else {
                $this->checkPermission('business_create');
                $row = new $this->businessClass();
                $row->status = 'publish';
                if (setting_item('business_vendor_create_service_must_approved_by_admin', 0)) {
                    $row->status = 'pending';
                }
                $row->author_id = Auth::id();
            }
            $dataKeys = [
                'title',
                'slug',
                'content',
                'image_id',
                'banner_image_id',
                'category_id',
                'location_id',
                'address',
                'map_lat',
                'map_lng',
                'map_zoom',
                'is_featured',
                'gallery',
                'video',
                'faqs',
                'price',
                'sale_price',
                'is_instant',
                'allow_children',
                'allow_infant',
                'max_guests',
                'bed',
                'bathroom',
                'square',
                'enable_extra_price',
                'extra_price',
                'discount_by_days',
                'default_state',
                'ical_import_url',
                'enable_service_fee',
                'service_fee',
                'surrounding',
                'min_day_before_booking',
                'min_day_stays',
                'franchising',
                'phone',
                'website',
                'ipanorama_id',
                'list_product',
                'items',
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
                        'status' => 1,
                        'message' => __('Business updated'),
                        'data' => $row
                    ]);
                } else {
                    event(new CreatedServicesEvent($row));
                    return response()->json([
                        'status' => 1,
                        'message' => __('Business created'),
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
     *     path="/api/user/business/{id}",
     *     tags={"Manage Business"},
     *     summary="Update an existing business",
     *     description="Update an existing business by ID. Requires business_update permission and ownership or business_manage_others permission.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Business ID to update",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Updated Grand Business", description="Business title"),
     *             @OA\Property(property="slug", type="string", maxLength=255, example="updated-grand-business", description="URL slug"),
     *             @OA\Property(property="content", type="string", example="Updated luxury business description", description="Business description"),
     *             @OA\Property(property="image_id", type="integer", example=1, description="Featured image ID"),
     *             @OA\Property(property="banner_image_id", type="integer", example=2, description="Banner image ID"),
     *             @OA\Property(property="category_id", type="integer", example=1, description="Business category ID"),
     *             @OA\Property(property="location_id", type="integer", example=1, description="Location ID"),
     *             @OA\Property(property="address", type="string", example="123 Main St, City", description="Business address"),
     *             @OA\Property(property="map_lat", type="string", example="40.7128", description="Latitude"),
     *             @OA\Property(property="map_lng", type="string", example="-74.0060", description="Longitude"),
     *             @OA\Property(property="map_zoom", type="integer", example=15, description="Map zoom level"),
     *             @OA\Property(property="is_featured", type="boolean", example=true, description="Is featured business"),
     *             @OA\Property(property="gallery", type="string", maxLength=255, example="3,4,5", description="Gallery image IDs (comma-separated)"),
     *             @OA\Property(property="video", type="string", maxLength=255, example="updated_business_video", description="Video URL"),
     *             @OA\Property(property="faqs", type="string", example="[{'question':'Updated FAQ','answer':'Updated answer'}]", description="Frequently asked questions"),
     *             @OA\Property(property="price", type="number", format="decimal", example=150.00, description="Base price"),
     *             @OA\Property(property="sale_price", type="number", format="decimal", example=120.00, description="Sale price"),
     *             @OA\Property(property="is_instant", type="boolean", example=true, description="Is instant booking enabled"),
     *             @OA\Property(property="allow_children", type="boolean", example=true, description="Allow children"),
     *             @OA\Property(property="allow_infant", type="boolean", example=false, description="Allow infants"),
     *             @OA\Property(property="max_guests", type="integer", example=25, description="Maximum number of guests"),
     *             @OA\Property(property="bed", type="integer", example=0, description="Number of beds (if applicable)"),
     *             @OA\Property(property="bathroom", type="integer", example=3, description="Number of bathrooms"),
     *             @OA\Property(property="square", type="integer", example=750, description="Square meters/feet"),
     *             @OA\Property(property="enable_extra_price", type="boolean", example=true, description="Enable extra pricing"),
     *             @OA\Property(property="extra_price", type="string", example="[{'name':'Updated Premium Service','price':'150','type':'one_time'}]", description="Extra price configuration"),
     *             @OA\Property(property="discount_by_days", type="string", example="[{'day_from':14,'discount':15}]", description="Discount by days configuration"),
     *             @OA\Property(property="default_state", type="boolean", example=true, description="Default state"),
     *             @OA\Property(property="ical_import_url", type="string", maxLength=191, example="https://updated-business.com/calendar.ics", description="iCal import URL"),
     *             @OA\Property(property="enable_service_fee", type="boolean", example=true, description="Enable service fee"),
     *             @OA\Property(property="service_fee", type="string", example="[{'name':'Updated Service Fee','price':'35'}]", description="Service fee configuration"),
     *             @OA\Property(property="surrounding", type="string", example="Updated surrounding description with new amenities", description="Surrounding area description"),
     *             @OA\Property(property="min_day_before_booking", type="integer", example=2, description="Minimum days before booking"),
     *             @OA\Property(property="min_day_stays", type="integer", example=3, description="Minimum days to stay"),
     *             @OA\Property(property="franchising", type="string", maxLength=191, example="Updated Franchise Group", description="Franchise information"),
     *             @OA\Property(property="phone", type="string", maxLength=191, example="+1-555-987-6543", description="Business phone number"),
     *             @OA\Property(property="website", type="string", maxLength=191, example="https://updated-business.com", description="Business website URL"),
     *             @OA\Property(property="ipanorama_id", type="integer", example=2, description="iPanorama ID"),
     *             @OA\Property(property="list_product", type="string", example="[{'name':'Updated Product A','price':'60'},{'name':'New Product C','price':'85'}]", description="Product listing"),
     *             @OA\Property(property="items", type="string", example="[{'name':'Updated Service A','description':'Enhanced premium service'},{'name':'New Service C','description':'Exclusive service'}]", description="Items/services offered"),
     *             @OA\Property(property="terms", type="array", @OA\Items(type="integer"), example={1,2,3}, description="Term IDs"),
     *             @OA\Property(property="lang", type="string", example="en", description="Language code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Business updated"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Business Stanford"),
     *                 @OA\Property(property="slug", type="string", example="business-stanford"),
     *                 @OA\Property(property="status", type="string", example="publish"),
     *                 @OA\Property(property="author_id", type="integer", example=1),
     *                 @OA\Property(property="price", type="number", format="decimal", example=300.00),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-05 12:33:42")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Business not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Business not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Insufficient permissions or not owner",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized to update this business")
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
     *         description="Service unavailable - Business service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Business service is not available")
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
     *     path="/api/user/business/{id}",
     *     tags={"Manage Business"},
     *     summary="Get business details",
     *     description="Retrieve detailed information about a specific business",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Business ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Business Stanford"),
     *                 @OA\Property(property="price", type="number", format="decimal", example=300.00),
     *                 @OA\Property(property="status", type="string", example="publish"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2023-07-18 09:03:18"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-09-05 12:33:42")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Business not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Business not found")
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
     *         description="Service unavailable - Business service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Business service is not available")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $business = $this->businessClass::find($id);

            if (empty($business)) {
                return response()->json(['message' => __('Business not found')], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $business
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'something went wrong'], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/business/{id}",
     *     tags={"Manage Business"},
     *     summary="Delete a business",
     *     description="Delete a business by ID. Requires business_delete permission and ownership of the business.",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Business ID to delete",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="permanently_delete",
     *         in="query",
     *         required=false,
     *         description="Permanently delete the business (soft delete by default)",
     *         @OA\Schema(type="boolean", example=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Business deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Delete business success!")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Business not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Business not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden - Insufficient permissions",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You do not have permission to delete businesses")
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
     *         description="Service unavailable - Business service is disabled",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Business service is not available")
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
            $this->checkPermission('business_delete');
            $user_id = Auth::id();
            if (\request()->query('permanently_delete')) {
                $query = $this->businessClass
                    ::where('author_id', $user_id)
                    ->where('id', $id)
                    ->withTrashed()
                    ->first();
                if (!empty($query)) {
                    $query->forceDelete();
                }
            } else {
                $query = $this->businessClass
                    ::where('author_id', $user_id)
                    ->where('id', $id)
                    ->first();
                if (!empty($query)) {
                    $query->delete();
                    event(new UpdatedServiceEvent($query));
                }
            }

            return response()->json([
                'status' => 1,
                'message' => __('Delete business success!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'something went wrong'
            ], 500);
        }
    }

    /**
     * Save business terms
     * 
     * @param Business $row
     * @param Request $request
     * @return void
     */
    public function saveTerms($row, $request)
    {
        if (empty($request->input('terms'))) {
            $this->businessTermClass::where('target_id', $row->id)->delete();
        } else {
            $term_ids = $request->input('terms');
            foreach ($term_ids as $term_id) {
                $this->businessTermClass::firstOrCreate([
                    'term_id' => $term_id,
                    'target_id' => $row->id,
                ]);
            }
            $this->businessTermClass
                ::where('target_id', $row->id)
                ->whereNotIn('term_id', $term_ids)
                ->delete();
        }
    }
}
