<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
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

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role_id != 1) {
            abort(403);
        }

        $data = [
            'breadcrumbs' => [
                [
                    'name' => __('Referral'),
                    'url'  => route('admin.referral.index')
                ],
                [
                    'name'  => __('Report'),
                    'class' => 'active'
                ],
            ],
            'page_title'  => __("Referral Report"),
            'referals'    => $this->bookingPayment::getReferralAdminHistory()->paginate(10),
        ];

        return view('admin.referral.index', $data);
    }
}

