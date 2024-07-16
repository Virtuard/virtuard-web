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


class ReferralController extends FrontendController
{
    protected $bookingClass;
    public function __construct()
    {
        $this->bookingClass = Booking::class;
        parent::__construct();
    }

    public function index(Request $request)
    {
        $data = [
            'bookings'    => $this->bookingClass::getReferralHistory($request->input('status') ?? 'paid', false, false, Auth::id()),
            'statues'     => config('booking.statuses'),
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
