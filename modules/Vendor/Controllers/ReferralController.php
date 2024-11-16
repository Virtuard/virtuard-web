<?php

namespace Modules\Vendor\Controllers;

use App\Helpers\ReCaptchaEngine;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rules\Password;
use Matrix\Exception;
use Modules\FrontendController;
use Modules\User\Events\NewVendorRegistered;
use Modules\User\Events\SendMailUserRegistered;
use Modules\Vendor\Models\VendorRequest;
use Modules\Booking\Models\Booking;
use Modules\Booking\Models\BookingPayment;

class ReferralController extends FrontendController
{
    protected $bookingClass;
    public function __construct()
    {
        $this->bookingClass = BookingPayment::class;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $user = Auth::user();
    
        if (!$user) {
            return redirect()->route('login')->withErrors(__('User not found. Please log in.'));
        }
    
        if ($user->role_id == 2) {
            $bookings = $this->bookingClass::getReferralHistory(Auth::id())->paginate(10);
        } elseif ($user->role_id == 1) {
            $bookings = $this->bookingClass::getReferralAdminHistory();
        } elseif ($user->role_id == 3) {
            $bookings = null;
        }
    
        $data = [
            'bookings'    => $bookings,
            'statuses'    => config('booking.statuses'),
            'userInfo'    => [
                'username' => $user->user_name,
                'userId'   => $user->id,
            ],
            'breadcrumbs' => [
                [
                    'name'  => __('Referral'),
                    'class' => 'active'
                ],
            ],
            'page_title'  => __("Referral Report"),
        ];
    
        return view('user.referral.index', $data);
    }
    
}
