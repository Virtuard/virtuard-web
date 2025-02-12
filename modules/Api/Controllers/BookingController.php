<?php

namespace Modules\Api\Controllers;

use Burtds\CashConverter\Facades\CashConverter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\Enquiry;
use Modules\Template\Models\Template;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;

class BookingController extends \Modules\Booking\Controllers\BookingController
{
    public function __construct(Booking $booking, Enquiry $enquiryClass)
    {
        parent::__construct($booking, $enquiryClass);
        $this->middleware('auth:api')->except([
            'detail',
            'getConfigs',
            'getHomeLayout',
            'getTypes',
            'cancelPayment',
            'thankyou',
            'bookingMidtrans'
        ]);
    }
    public function getTypes()
    {
        $types = get_bookable_services();

        $res = [];
        foreach ($types as $type => $class) {
            $obj = new $class();
            $res[$type] = [
                'icon' => call_user_func([$obj, 'getServiceIconFeatured']),
                'name' => call_user_func([$obj, 'getModelName']),
                'search_fields' => [],
            ];
        }
        return $res;
    }

    public function getConfigs()
    {
        $languages = \Modules\Language\Models\Language::getActive();
        $template = Template::find(setting_item('api_app_layout'));
        $res = [
            'languages' => $languages->map(function ($lang) {
                return $lang->only(['locale', 'name']);
            }),
            'booking_types' => $this->getTypes(),
            'country' => get_country_lists(),
            'app_layout' => $template ? json_decode($template->content, true) : [],
            'is_enable_guest_checkout' => (int)is_enable_guest_checkout(),
            'service_search_forms' => [],
            'locale' =>  session('website_locale', app()->getLocale()),
            'currency_main' => \App\Currency::getCurrent('currency_main'),
            'currency' => $this->getCurrency()
        ];
        $all_service = get_bookable_services();
        foreach ($all_service as $key => $class) {
            $res['service_search_forms'][$key] = call_user_func([$class, 'getFormSearch'], request());
        }
        return $this->sendSuccess($res);
    }

    /**
     * @OA\Get(
     *     path="/api/home-page",
     *     tags={"Page"},
     *     summary="Get home page",
     *     description="Retrieve the processed content of the home page.",
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with home page data",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="type", type="string", example="list_hotel"),
     *                     @OA\Property(property="name", type="string", example="Hotel: List Items"),
     *                     @OA\Property(property="model", type="object",
     *                         @OA\Property(property="title", type="string", example="Accomodation"),
     *                         @OA\Property(property="desc", type="string", example="Recommended Accomodation"),
     *                         @OA\Property(property="number", type="integer", example=6),
     *                         @OA\Property(property="style", type="string", example="carousel"),
     *                         @OA\Property(property="location_id", type="string", example=""),
     *                         @OA\Property(property="order", type="string", example="id"),
     *                         @OA\Property(property="order_by", type="string", example="desc"),
     *                         @OA\Property(property="is_featured", type="boolean", example=false),
     *                         @OA\Property(property="custom_ids", type="array", @OA\Items(type="integer")),
     *                         @OA\Property(property="data", type="array",
     *                             @OA\Items(
     *                                 type="object",
     *                                 @OA\Property(property="id", type="integer", example=1),
     *                                 @OA\Property(property="object_model", type="string", example="hotel"),
     *                                 @OA\Property(property="title", type="string", example="My Hotel"),
     *                                 @OA\Property(property="price", type="number", example=100)
     *                             )
     *                         )
     *                      )
     *                   )
     *              )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Template not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Template not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Unable to retrieve home layout.")
     *         )
     *     )
     * )
     */

    public function getHomeLayout()
    {
        $res = [];
        $template = Template::find(setting_item('api_app_layout'));
        if (!empty($template)) {
            $translate = $template->translate();
            $res = $translate->getProcessedContentAPI();
        }
        return $this->sendSuccess(
            [
                "data" => $res
            ]
        );
    }


    protected function validateCheckout($code)
    {

        $booking = $this->booking::where('code', $code)->first();

        $this->bookingInst = $booking;

        if (empty($booking)) {
            abort(404);
        }

        return true;
    }

    public function detail(Request $request, $code)
    {

        $booking = Booking::where('code', $code)->first();
        if (empty($booking)) {
            return $this->sendError(__("Booking not found!"))->setStatusCode(404);
        }

        if ($booking->status == 'draft') {
            return $this->sendError(__("You do not have permission to access"))->setStatusCode(404);
        }
        $data = [
            'booking'    => $booking,
            'service'    => $booking->service,
        ];
        if ($booking->gateway) {
            $data['gateway'] = get_payment_gateway_obj($booking->gateway);
        }
        return $this->sendSuccess(
            $data
        );
    }

    protected function validateDoCheckout()
    {

        $request = \request();
        /**
         * @param Booking $booking
         */
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('', ['errors' => $validator->errors()]);
        }
        $code = $request->input('code');
        $booking = $this->booking::where('code', $code)->first();
        $this->bookingInst = $booking;

        if (empty($booking)) {
            abort(404);
        }

        return true;
    }

    public function checkStatusCheckout($code)
    {
        $booking = $this->booking::where('code', $code)->first();
        $data = [
            'error'    => false,
            'message'  => '',
            'redirect' => ''
        ];
        if (empty($booking)) {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }

        if ($booking->status != 'draft') {
            $data = [
                'error'    => true,
                'redirect' => url('/')
            ];
        }
        return response()->json($data, 200);
    }

    public function getGatewaysForApi()
    {
        $res = [];
        $gateways = $this->getGateways();
        foreach ($gateways as $gateway => $obj) {
            $res[$gateway] = [
                'logo' => $obj->getDisplayLogo(),
                'name' => $obj->getDisplayName(),
                'desc' => $obj->getApiDisplayHtml(),
            ];
            if ($option = $obj->getForm()) {
                $res[$gateway]['form'] = $option;
            }
            if ($options = $obj->getApiOptions()) {
                $res[$gateway]['options'] = $options;
            }
        }

        return $this->sendSuccess($res);
    }

    public function thankyou(Request $request, $code)
    {

        $booking = Booking::where('code', $code)->first();
        if (empty($booking)) {
            abort(404);
        }

        if ($booking->status == 'draft') {
            return redirect($booking->getCheckoutUrl());
        }

        $data = [
            'page_title' => __('Booking Details'),
            'booking'    => $booking,
            'service'    => $booking->service,
        ];
        if ($booking->gateway) {
            $data['gateway'] = get_payment_gateway_obj($booking->gateway);
        }
        return view('Booking::frontend/detail', $data);
    }

    public function getCurrency()
    {
        $list = \App\Currency::getActiveCurrency();
        foreach ($list as &$item) {
            $currency = \App\Currency::getCurrency($item['currency_main']);
            $item['symbol'] = $currency['symbol'];
        }
        return $list;
    }

    public function bookingMidtrans(Request $request)
{
    if (!auth()->check()) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Unauthorized. Please login first.',
        ], 401);
    }

    $user = auth()->user();

    $validator = Validator::make($request->all(), [
        'fee'          => 'required|numeric|min:0',
        'start_date'   => 'required|date|after_or_equal:today',
        'end_date'     => 'required|date|after_or_equal:start_date',
        'object_id'    => 'required|integer',
        'object_model' => 'required|string',
        'vendor_id'    => 'required|integer',
        'total_guests' => 'required|integer',
        'room_id' => 'required|integer',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'status'  => 'error',
            'message' => $validator->errors(),
        ], 422);
    }

    $start_date = Carbon::parse($request->start_date);
    $end_date   = Carbon::parse($request->end_date);
    $jumlah_hari = $start_date->diffInDays($end_date) + 1; 

    $total      = $request->fee * $jumlah_hari;
    $commission = ($total * 10) / 100;

    $order = Booking::create([
        'customer_id'          => $user->id,
        'email'                => $user->email,
        'first_name'           => $user->first_name,
        'last_name'            => $user->last_name,
        'phone'                => $user->phone,
        'object_id'            => $request->object_id,
        'object_model'         => $request->object_model,
        'vendor_id'            => $request->vendor_id,
        'total_guests'         => $request->total_guests,
        'status'               => 'processing',
        'gateway'              => 'midtrans',
        'total'                => $total,
        'total_before_fees'    => $total,
        'total_before_discount'=> $total,
        'pay_now'              => $total,
        'commission'           => $commission,
        'commission_type'      => json_encode([
            'amount' => '10',
            'type'   => 'percent'
        ]),
        'start_date'           => $request->start_date,
        'end_date'             => $request->end_date,
    ]);

    DB::table('bravo_hotel_room_bookings')->insert([
        'room_id'     => $request->room_id, 
        'parent_id'   => $request->object_id, 
        'booking_id'  => $order->id, 
        'start_date'  => $request->start_date,
        'end_date'    => $request->end_date,
        'number'      => $request->total_guests,
        'price'       => $total,
        'create_user' => $user->id,
        'update_user' => $user->id,
        'created_at'  => now(),
        'updated_at'  => now(),
    ]);

    Config::$serverKey = config('midtrans.server_key');
    Config::$clientKey = config('midtrans.client_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    $convertIdr = CashConverter::convert('USD','IDR', $total);
    $convertIdr = round($convertIdr);

    $transaction = [
        'transaction_details' => [
            'order_id'    => $order->code,
            'gross_amount' => $convertIdr,
        ],
        'customer_details' => [
            'first_name' => $user->first_name,
            'last_name'  => $user->last_name,
            'email'      => $user->email,
            'phone'      => $user->phone,
        ],
    ];

    try {
        $snapResponse = Snap::createTransaction($transaction);

        return response()->json([
            'status'      => 'success',
            'message'     => 'Order created successfully',
            'order'       => $order,
            'total'       => $total,
            'payment_url' => $snapResponse->redirect_url,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to generate Midtrans payment URL: ' . $e->getMessage(),
        ], 500);
    }
}



}


// Config::$serverKey = 'SB-Mid-server-K4Ram42syLa3wOOSCJs0MuSA';
// Config::$clientKey = 'SB-Mid-client-Q2tFhu9EIkOFHwNg';
// Config::$isProduction = false;
// Config::$serverKey = config('midtrans.server_key');
    // Config::$clientKey = config('midtrans.client_key');
    // Config::$isProduction = config('midtrans.is_production');