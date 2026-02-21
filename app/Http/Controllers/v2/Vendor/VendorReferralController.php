<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\BookingPayment;
use Modules\Booking\Models\Booking;
use Illuminate\Pagination\LengthAwarePaginator;

class VendorReferralController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Vendor Referral Page (V2)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $typeFilter = $request->input('type', 'plan'); // 'plan', 'service'
        $sortFilter = $request->input('sort', 'latest'); // 'latest', 'this_month'
        $planFilter = $request->input('plan_type', 'all'); // 'all', 1, 2, 3...
        $serviceFilter = $request->input('service_type', 'all'); // 'all', 'custom 360 tour creation', 'vr shooting service', 'hosting extension'

        $referralLink = url('/register?ref=' . $userId);

        $referrals = collect();
        $paginator = null;

        if ($typeFilter === 'plan') {
            // Plan Referrals from BookingPayment (bravo_user_plan table)
            // Using the API query pattern but manipulating correctly
            $query = BookingPayment::query()
                ->join("users", "users.id", "=", "bravo_user_plan.referal_user_id")
                ->join("bravo_plans", "bravo_plans.id", "=", "bravo_user_plan.plan_id")
                ->select(
                    "bravo_user_plan.id",
                    "bravo_user_plan.referal_amount AS amount",
                    "bravo_user_plan.created_at",
                    "users.name as user_name",
                    "bravo_plans.title as plan_name",
                    "bravo_user_plan.plan_id"
                )
                ->where('bravo_user_plan.user_id', $userId) // As per API controller referal check
                ->where('bravo_user_plan.status', 1);

            if ($planFilter !== 'all') {
                $query->where('bravo_user_plan.plan_id', $planFilter);
            }

            if ($sortFilter === 'this_month') {
                $query->whereMonth('bravo_user_plan.created_at', now()->month)
                    ->whereYear('bravo_user_plan.created_at', now()->year);
            }

            $query->orderBy('bravo_user_plan.created_at', 'desc');

            $paginator = $query->paginate(10)->appends($request->query());

            $paginator->through(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'Plan Subscription',
                    'detail' => $item->plan_name,
                    'user' => $item->user_name,
                    'date' => $item->created_at ? $item->created_at->format('M d, Y') : '',
                    'commission' => format_money($item->amount),
                ];
            });

        } else {
            // Service Referrals from Booking table (where ref_id matches)
            $query = Booking::query()
                ->with(['service', 'customer'])
                ->where('ref_id', $userId)
                ->where('status', '!=', 'draft');

            if ($serviceFilter !== 'all') {
                // If filtering by service title or type string
                $query->whereHas('service', function ($q) use ($serviceFilter) {
                    $q->where('title', 'like', '%' . $serviceFilter . '%');
                });
            }

            if ($sortFilter === 'this_month') {
                $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            }

            $query->orderBy('created_at', 'desc');

            $paginator = $query->paginate(10)->appends($request->query());

            $paginator->through(function ($booking) {
                $serviceTitle = $booking->service ? $booking->service->title : 'N/A';
                return [
                    'id' => $booking->id,
                    'type' => ucfirst($booking->object_model) . ' Service',
                    'detail' => $serviceTitle,
                    'user' => $booking->customer ? $booking->customer->name : $booking->first_name . ' ' . $booking->last_name,
                    'date' => $booking->created_at ? $booking->created_at->format('M d, Y') : '',
                    'commission' => format_money($booking->ref_commission), // Adjust based on DB structure, assuming ref_commission holds referral cut
                ];
            });
        }

        // Get Available Plans for Dropdown Filter
        $allPlans = \Modules\User\Models\Plan::query()->where('status', 'publish')->get(['id', 'title']);

        $data = [
            'page_title' => __('Referral'),
            'referrals' => $paginator,
            'referral_link' => $referralLink,
            'filters' => [
                'type' => $typeFilter,
                'sort' => $sortFilter,
                'plan_type' => $planFilter,
                'service_type' => $serviceFilter,
            ],
            'available_plans' => $allPlans,
            'available_services' => [
                'custom 360 tour creation',
                'vr shooting service',
                'hosting extension'
            ]
        ];

        return view('v2.vendor.referral.index', $data);
    }
}
