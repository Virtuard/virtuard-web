<?php
namespace Modules\Api\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Modules\Booking\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Location\Models\Location;
use Modules\User\Models\UserWishList;


class UserController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function getBookingHistory(Request $request)
{
    try {
        $user_id = Auth::id();

        $bookings = Booking::where(function ($query) use ($user_id) {
            $query->where('create_user', $user_id)
                  ->orWhere('customer_id', $user_id);
        })
        ->when($request->input('status'), function ($query, $status) {
            return $query->where('status', $status);
        })
        ->get();
    
        $bookingsWithServiceTitle = $bookings->map(function ($booking) {
            if ($booking->service) {
                $booking->service_title = $booking->service->title;
            } else {
                $booking->service_title = null; 
            }
            return $booking;
        });
    
        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookingsWithServiceTitle,
            ],
            'message' => __('Booking history retrieved successfully'),
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => __('An error occurred while retrieving booking history'),
            'error' => $e->getMessage(),
        ], 500);
    }
}
    
    // public function getBookingHistory(Request $request){
    //     $user_id = Auth::id();
    //     $query = Booking::getBookingHistory($request->input('status'), $user_id);
    //     $rows = [];
    //     foreach ($query as $item){
    //         $service = $item->service;
    //         $serviceTranslation = $service->translate();
    //         $meta_tmp = $item->getAllMeta();
    //         $item = $item->toArray();
    //         $meta = [];
    //         if(!empty($meta_tmp)){
    //             foreach ( $meta_tmp as $val){
    //                 $meta[ $val->name ] = !empty($json = json_decode($val->val,true)) ? $json : $val->val  ;
    //             }
    //         }
    //         $item['commission_type'] = json_decode( $item['commission_type'] , true);
    //         $item['buyer_fees'] = json_decode( $item['buyer_fees'] , true);
    //         $item['booking_meta'] = $meta;
    //         $item['service_icon'] = $service->getServiceIconFeatured() ?? null;
    //         $item['service'] = ['title'=>$serviceTranslation->title];
    //         $rows[] = $item;
    //     }
    //     return $this->sendSuccess([
    //         'data'=> $rows,
    //         'total'=>$query->total(),
    //         'max_pages'=>$query->lastPage()
    //     ]);
    // }

    /**
     * @OA\Post(
     *     path="/api/user/wishlist",
     *     tags={"Wishlist"},
     *     summary="Add or remove service from wishlist",
     *     description="Toggle wishlist status for a service. If service is already in wishlist, it will be removed. Otherwise, it will be added.",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"object_id", "object_model"},
     *             @OA\Property(property="object_id", type="integer", example=1, description="Service ID"),
     *             @OA\Property(property="object_model", type="string", example="hotel", description="Service type (hotel, space, business, etc.)")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wishlist status toggled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="class", type="string", example="active", description="'active' if added, empty string if removed")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Service ID is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function handleWishList(Request $request){
        $class = new \Modules\User\Controllers\UserWishListController();
        return $class->handleWishList($request);
    }

    /**
     * @OA\Get(
     *     path="/api/user/wishlist",
     *     tags={"Wishlist"},
     *     summary="Get user's wishlist",
     *     description="Retrieve paginated list of services in user's wishlist",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Page number",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Items per page",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Wishlist retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="object_id", type="integer", example=1),
     *                     @OA\Property(property="object_model", type="string", example="hotel"),
     *                     @OA\Property(property="user_id", type="integer", example=1),
     *                     @OA\Property(property="service", type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Grand Hotel"),
     *                         @OA\Property(property="price", type="number", format="float", example=100.00),
     *                         @OA\Property(property="sale_price", type="number", format="float", example=80.00),
     *                         @OA\Property(property="image", type="string", example="http://example.com/image.jpg"),
     *                         @OA\Property(property="review_score", type="number", format="float", example=4.5)
     *                     )
     *                 )
     *             ),
     *             @OA\Property(property="total", type="integer", example=10),
     *             @OA\Property(property="total_pages", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function indexWishlist(Request $request){
        $perPage = $request->query('per_page', 5);
        $query = UserWishList::query()
            ->where("user_wishlist.user_id",Auth::id())
            ->orderBy('user_wishlist.id', 'desc')
            ->paginate($perPage);
        $rows = [];
        foreach ($query as $item){
            $service = $item->service;
            if(empty($service)) continue;

            $item = $item->toArray();
            $serviceTranslation = $service->translate();
            $item['service'] = [
                'id'=>$service->id,
                'title'=>$serviceTranslation->title,
                'price'=>$service->price,
                'sale_price'=>$service->sale_price,
                'discount_percent'=>$service->discount_percent ?? null,
                'image'=>get_file_url($service->image_id),
                'content'=>$serviceTranslation->content,
                'location' => Location::selectRaw("id,name")->find($service->location_id) ?? null,
                'is_featured' => $service->is_featured ?? null,
                'service_icon' => $service->getServiceIconFeatured() ?? null,
                'review_score' =>  $service->getScoreReview() ?? null,
                'service_type' =>  $service->getModelName() ?? null,
            ];
            $rows[] = $item;
        }
        return $this->sendSuccess(
            [
                'data'=>$rows,
                'total'=>$query->total(),
                'total_pages'=>$query->lastPage(),
            ]
        );
    }

    public function permanentlyDelete(Request  $request)
    {
        return (new \Modules\User\Controllers\UserController())->permanentlyDelete($request);
    }
}
