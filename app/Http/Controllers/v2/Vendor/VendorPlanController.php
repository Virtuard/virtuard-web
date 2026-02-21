<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Models\UserPlan;
use Modules\User\Models\Plan;

class VendorPlanController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Vendor My Plans Page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Check overall plan validity (optional depending on frontend use case)
        $hasActivePlan = $user->checkUserPlanStatus();

        // Available plan types from `bravo_plans` for the filter dropdown
        $allPlans = Plan::query()
            ->where('status', 'publish')
            ->get(['id', 'title']);

        // Query to get user plans
        $query = UserPlan::query()
            ->with(['plan']) // loaded relations
            ->where('user_id', $user->id);

        /**
         * Filtering
         */
        $statusFilter = $request->input('status', 'all'); // 'active', 'expired', 'cancelled', 'all'
        $planFilter = $request->input('plan_id', 'all'); // plan ID or 'all'
        $sortFilter = $request->input('sort', 'latest_expiry'); // 'latest_expiry', 'oldest_expiry'

        // Filter by Plan Name/ID
        if ($planFilter !== 'all' && !empty($planFilter)) {
            $query->where('plan_id', $planFilter);
        }

        // Filter by Status
        if ($statusFilter === 'active') {
            $query->where('status', 1)->where('end_date', '>', now());
        } elseif ($statusFilter === 'expired') {
            $query->where('status', 1)->where('end_date', '<=', now());
        } elseif ($statusFilter === 'cancelled') {
            $query->where('status', 0);
        }

        // Sorting by Expiry
        if ($sortFilter === 'oldest_expiry') {
            $query->orderBy('end_date', 'asc');
        } else {
            $query->orderBy('end_date', 'desc');
        }

        $userPlans = $query->paginate(10)->appends($request->query());

        $userPlans->through(function ($userPlan) {
            $planName = $userPlan->plan->title ?? 'N/A';

            $currentStatus = 'Cancelled';
            if ($userPlan->status == 1) {
                if ($userPlan->end_date && $userPlan->end_date <= now()) {
                    $currentStatus = 'Expired';
                } else {
                    $currentStatus = 'Active';
                }
            }

            return [
                'id' => $userPlan->id,
                'plan_name' => $planName,
                'expiry' => $userPlan->end_date ? $userPlan->end_date->format('d M Y') : 'Lifetime',
                'total_service' => $userPlan->max_service ?? 'Unlimited', // Assuming null means unlimited if 0 is not the bound
                'total_ipanorama' => $userPlan->max_ipanorama ?? 0,
                'price' => format_money($userPlan->price),
                'status' => $currentStatus, // e.g. "Active", "Expired"
            ];
        });

        $data = [
            'page_title' => __('My Plans'),
            'user_plans' => $userPlans,
            'all_plans' => $allPlans,
            'renew_plan_url' => route('plan'),
            'filters' => [
                'sort' => $sortFilter,
                'status' => $statusFilter,
                'plan_id' => $planFilter
            ]
        ];

        return view('v2.vendor.my-plans.index', $data);
    }
}
