<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingPayment;

class ReferralController extends Controller
{
    protected $bookingClass;
    protected $bookingPayment;
    public function __construct()
    {
        $this->bookingClass = Booking::class;
        $this->bookingPayment = BookingPayment::class;
    }

    public function getReports(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }   

        if ($user->role_id == 2) {
            $bookings = $this->bookingPayment::getReferralHistoryAPI(Auth::id())->paginate(10);
        } 
        $data = [
            'referals'    => $bookings,
            'statues'     => config('booking.statuses'),
            'userInfo'    => [
                'username' => $user->user_name,
                'userId'   => $user->id,
            ],

        ];


        return response()->json([
            'success' => 1,
            'data'  => $data,
        ], 200);




    }

}