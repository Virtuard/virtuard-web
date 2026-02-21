<?php

namespace App\Http\Controllers\v2\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\User\Models\Plan;
use Modules\User\Models\PlanPayment;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Checkout page for a plan.
     */
    public function index(Request $request, $planId)
    {
        if (!is_enable_plan()) {
            return redirect('/');
        }

        $plan = Plan::where('status', 'publish')->findOrFail($planId);
        $user = Auth::user();

        // Check role match
        if ($user->role_id != $plan->role_id) {
            return redirect()->route('plan')
                ->with('warning', __('This plan is not suitable for your role.'));
        }

        $isAnnual = !empty($request->query('annual'));

        // Price calculation
        $basePrice = $isAnnual ? ($plan->annual_price ?: $plan->price) : $plan->price;

        // Affiliate discount
        $hasAffiliatePlan = $this->hasAffiliatePlan($user);
        $discount = $hasAffiliatePlan ? $basePrice * 0.1 : 0;
        $finalPrice = $basePrice - $discount;

        // Tax (configurable)
        $taxRate = 0; // 0%
        $taxAmount = $finalPrice * ($taxRate / 100);
        $totalPayment = $finalPrice + $taxAmount;

        $data = [
            'page_title' => __('Checkout'),
            'plan' => [
                'id' => $plan->id,
                'title' => $plan->title,
                'price' => $plan->price,
                'annual_price' => $plan->annual_price,
                'duration' => $plan->duration,
                'duration_type' => $plan->duration_type,
                'duration_text' => $plan->duration_text,
                'max_service' => $plan->max_service,
                'max_ipanorama' => $plan->max_ipanorama,
            ],
            'user' => [
                'id' => $user->id,
                'business_name' => $user->business_name,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'phone' => $user->phone,
                'birthday' => $user->birthday,
                'address' => $user->address,
                'address2' => $user->address2,
                'city' => $user->city,
                'state' => $user->state,
                'country' => $user->country,
                'zip_code' => $user->zip_code,
            ],
            'payment' => [
                'base_price' => $basePrice,
                'discount' => $discount,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'total' => $totalPayment,
                'is_annual' => $isAnnual,
                'has_affiliate' => $hasAffiliatePlan,
            ],
        ];

        return view('v2.checkout.index', $data);
    }

    /**
     * Process checkout (existing logic via PlanController@buyProcess).
     */
    public function process(Request $request, $planId)
    {
        return app(\Modules\User\Controllers\PlanController::class)->buyProcess($request, $planId);
    }

    /**
     * Confirm plan payment page (Midtrans Snap popup).
     */
    public function confirmPlan($code)
    {
        $payment = PlanPayment::where('code', $code)->firstOrFail();

        return view('v2.checkout.confirm-plan', [
            'page_title' => __('Confirm Payment'),
            'payment' => $payment,
            'snapToken' => $payment->getMeta('snap_token'),
        ]);
    }

    private function hasAffiliatePlan($user): bool
    {
        $hasValidUserPlan = DB::table('bravo_booking_payments')
            ->where('create_user', $user->id)
            ->whereNotNull('affiliate_id')
            ->where('status', 'completed')
            ->exists();

        if ($hasValidUserPlan) {
            return false;
        }

        return !empty($user->affiliate_plan_user_id);
    }
}
