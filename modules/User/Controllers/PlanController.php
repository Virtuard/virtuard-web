<?php

namespace Modules\User\Controllers;

use App\Helpers\ReCaptchaEngine;
use Burtds\CashConverter\Facades\CashConverter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;
use Modules\FrontendController;
use Modules\User\Events\CreatePlanRequest;
use Modules\User\Events\UpdatePlanRequest;
use Modules\User\Models\Plan;
use Modules\User\Models\PlanPayment;
use Modules\User\Models\UserPlan;
use Worksome\Exchange\Facades\Exchange;

class PlanController extends FrontendController
{
    public function index()
    {
        if (!is_enable_plan()) {
            return redirect('/');
        }
        if (!auth()->check()) {
            // return redirect(route('login'));
            $plans = Plan::query()
                ->whereStatus('publish')
                ->get();

            $data = [
                'page_title' => __('Pricing Packages'),
                'plans' => $plans,
                'user' => collect(),
                'hasAffiliatePlan' => collect()
            ];
        } else {
            $user = auth()->user();

            $hasValidUserPlan = DB::table('bravo_booking_payments')
                ->where('create_user', $user->id)
                ->whereNotNull('affiliate_id')
                ->where('status', 'completed')
                ->exists();

            if ($hasValidUserPlan) {
                $hasAffiliatePlan =  false;
            } else {
                $hasAffiliatePlan = !empty($user->affiliate_plan_user_id);
            }

            $plans = Plan::query()
                ->where('role_id', $user->role_id)
                ->whereStatus('publish')
                ->get();

            $data = [
                'page_title' => __('Pricing Packages'),
                'plans' => $plans,
                'user' => $user,
                'hasAffiliatePlan' => $hasAffiliatePlan
            ];
        }

        return view('User::frontend.plan.index', $data);
    }


    public function myPlan()
    {
        if (!is_enable_plan()) {
            return redirect('/');
        }
        if (!auth()->user()->user_plan) {
            return redirect(route('plan'));
        }
        $user = auth()->user();


        $plans = Plan::query()
            ->where('role_id', auth()->user()->role_id)
            ->whereStatus('publish')
            ->get();


        $hasValidUserPlan = DB::table('bravo_booking_payments')
            ->where('create_user', $user->id)
            ->whereNotNull('affiliate_id')
            ->where('status', 'completed')
            ->exists();

        if ($hasValidUserPlan) {
            $hasAffiliatePlan =  false;
        } else {
            $hasAffiliatePlan = !empty($user->affiliate_plan_user_id);
        }

        $data = [
            'user' => auth()->user(),
            'page_title' => __('My Plan'),
            'menu_active' => 'my_plan',
            'hasAffiliatePlan' => $hasAffiliatePlan,

            'breadcrumbs' => [
                [
                    'name' => __('My plans'),
                    'class' => 'active',
                ],
            ],
            'plans' => $plans,
        ];
        return view('User::frontend.plan.my-plan', $data);
    }

    public function buy(Request $request, $id)
    {
        if (!is_enable_plan()) {
            return redirect('/');
        }
        $plan = Plan::find($id);
        if (!$plan) {
            return;
        }

        $user = auth()->user();

        $hasValidUserPlan = DB::table('bravo_booking_payments')
            ->where('create_user', $user->id)
            ->whereNotNull('affiliate_id')
            ->where('status', 'completed')
            ->exists();

        if ($hasValidUserPlan) {
            $hasAffiliatePlan =  false;
        } else {
            $hasAffiliatePlan = !empty($user->affiliate_plan_user_id);
        }


        $plan_page = route('plan');
        $gateways = app()
            ->make(\Modules\Booking\Controllers\BookingController::class)
            ->getGateways();

        if ($user->role_id != $plan->role_id) {
            return redirect()
                ->to($plan_page)
                ->with('warning', __('This plan is not suitable for your role.'));
        }

        if ($request->query('annual') and !$plan->annual_price) {
            return redirect()
                ->to($plan_page)
                ->with('warning', __("This plan doesn't have annual pricing"));
        }

        return view('User::frontend.plan.checkout', [
            'plan' => $plan,
            'user' => $user,
            'gateways' => $gateways,
            'hasAffiliatePlan' => $hasAffiliatePlan

        ]);
    }


    public function buyProcess(Request $request, $id)
    {
        $plan = Plan::find($id);
        if (!$plan) {
            return;
        }
        $user = auth()->user();
        $rules = [];
        $message = [];

        // $payment_gateway = $request->input('payment_gateway');
        // $gateways = get_payment_gateways();
        // if (empty($payment_gateway)) {
        //     return redirect()
        //         ->back()
        //         ->with('error', __('Please select payment gateway'));
        // }
        // if (empty($payment_gateway) or empty($gateways[$payment_gateway]) or !class_exists($gateways[$payment_gateway])) {
        //     return redirect()
        //         ->back()
        //         ->with('error', __('Payment gateway not found'));
        // }
        // $gatewayObj = new ($gateways[$payment_gateway])($payment_gateway);
        // if (!$gatewayObj->isAvailable()) {
        //     return redirect()
        //         ->back()
        //         ->with('error', __('Payment gateway is not available'));
        // }
        // if ($gRules = $gatewayObj->getValidationRules()) {
        //     $rules = array_merge($rules, $gRules);
        // }
        // if ($gMessages = $gatewayObj->getValidationMessages()) {
        //     $message = array_merge($message, $gMessages);
        // }

        $rules['term_conditions'] = 'required';

        /**
         * Google ReCapcha
         */
        $is_api = request()->segment(1) == 'api';

        if (!$is_api and ReCaptchaEngine::isEnable() and setting_item('booking_enable_recaptcha')) {
            $codeCapcha = $request->input('g-recaptcha-response');
            if (!$codeCapcha or !ReCaptchaEngine::verify($codeCapcha)) {
                return redirect()
                    ->back()
                    ->with('error', __('Please verify the captcha'));
            }
        }

        $messages['term_conditions.required'] = __('Term conditions is required field');
        $messages['payment_gateway.required'] = __('Payment gateway is required field');
        $validator = Validator::make($request->all(), $rules, $message);
        if ($validator->fails()) {
            if (is_array($validator->errors()->messages())) {
                $msg = '';
                foreach ($validator->errors()->messages() as $oneMessage) {
                    $msg .= implode('<br/>', $oneMessage) . '<br/>';
                }
                return redirect()
                    ->back()
                    ->with('error', $msg);
            }
            return redirect()
                ->back()
                ->with('error', $validator->errors());
        }

        if (!$plan->price || $plan->price == 0) {
            // For Free
            $new_user_plan = UserPlan::query()->where('user_id', $user->id)->where('plan_id', $plan->id)->first();
            if (empty($new_user_plan)) {
                $new_user_plan = new UserPlan();
                // $new_user_plan->id = $user->id;
            }
            $new_user_plan->plan_id = $id;
            $new_user_plan->price = $plan->price;
            $new_user_plan->start_date = date('Y-m-d H:i:s');
            if ($plan->duration) {
                $new_user_plan->end_date = date('Y-m-d H:i:s', strtotime('+ ' . $plan->duration . ' ' . $plan->duration_type));
            }
            $new_user_plan->max_service = $plan->max_service;
            $new_user_plan->max_ipanorama = $plan->max_ipanorama;
            $new_user_plan->plan_data = $plan;
            $new_user_plan->user_id = \Auth::id();

            //new referal concept
            $new_user_plan->referal_user_id = $user->affiliate_plan_user_id;
            $new_user_plan->referal_amount = $plan->price * 0.1;

            $new_user_plan->save();

            event(new UpdatePlanRequest($user));

            return redirect()
                ->route('user.plan')
                ->with('success', __('Purchased user package successfully'));
        } else {
            $hasValidUserPlan = DB::table('bravo_booking_payments')
                ->where('create_user', $user->id)
                ->whereNotNull('affiliate_id')
                ->where('status', 'completed')
                ->exists();

            if ($hasValidUserPlan) {
                $hasAffiliatePlan =  false;
            } else {
                $hasAffiliatePlan = !empty($user->affiliate_plan_user_id);
            }

            $is_annual = !empty($request->input('annual')) ? true : false;

            $payment = new PlanPayment();
            $payment->object_model = 'plan';
            $payment->object_id = $plan->id;
            $payment->status = 'draft';
            $payment->payment_gateway = 'midtrans';
            $payment->amount =  $hasAffiliatePlan ? ($is_annual ? $plan->annual_price * 0.9 : $plan->price * 0.9) : ($is_annual ? $plan->annual_price : $plan->price);
            $payment->user_id = auth()->id();
            $payment->affiliate_id = $user->affiliate_plan_user_id;

            $payment->save();
            $payment->addMeta('user_request', $user->id);
            $payment->addMeta('annual', $is_annual);

            $user->applyPlan($plan, $payment->amount, $is_annual, false);

            // dd($payment->amount);

            Config::$serverKey = config('midtrans.server_key');
            Config::$clientKey = config('midtrans.client_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$is3ds = true;
            Config::$isSanitized = true;

           $convertIdr = CashConverter::convert('USD','IDR', $payment->amount);
           $convertIdr = round($convertIdr);

            $transactionDetails = [
                'order_id' => $payment->code,
                'gross_amount' =>  $convertIdr,
            ];

            $itemDetails = [
                [
                    'id' => 'Plan' . $plan->id,
                    'price' => $convertIdr,
                    'quantity' => 1,
                    'name' => 'Plan #' . $payment->id
                ]
            ];

            $customerDetails = [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ];

            $transaction = [
                'payment_type' => 'credit_card',
                'credit_card' => [
                    'secure' => true
                ],
                'transaction_details' => $transactionDetails,
                'item_details' => $itemDetails,
                'customer_details' => $customerDetails,
            ];

            $snapToken = Snap::getSnapToken($transaction);

            $payment->addMeta('snap_token', $snapToken);

            return redirect()->route('frontend.plan.confirm-plan', ['code' => $payment->code]);


            // $res = $gatewayObj->processNormal($payment);
            // $success = $res[0] ?? null;
            // $message = $res[1] ?? null;
            // $redirect_url = $res[2] ?? null;
            // if ($success) {
            //     event(new CreatePlanRequest($user));

            //     $new_user_plan = UserPlan::query()->where('user_id', $user->id)
            //         ->where('plan_id', $plan->id)->where('price', $payment->amount)

            //         ->first();
            //     if (empty($new_user_plan)) {
            //         // $new_user_plan = new UserPlan();
            //     }

            //     $new_user_plan->plan_id = $plan->id;
            //     $new_user_plan->price = $payment->amount;
            //     $new_user_plan->start_date = date('Y-m-d H:i:s');
            //     if ($plan->duration) {
            //         $new_user_plan->end_date = date('Y-m-d H:i:s', strtotime('+ ' . $plan->duration . ' ' . $plan->duration_type));
            //     }
            //     $new_user_plan->max_service = $plan->max_service;
            //     $new_user_plan->max_ipanorama = $plan->max_ipanorama;
            //     $new_user_plan->plan_data = $plan;
            //     $new_user_plan->user_id = $user->id;
            //     $new_user_plan->referal_user_id = $user->affiliate_plan_user_id;
            //     $new_user_plan->referal_amount = $plan->price * 0.1;

            //     $new_user_plan->save();


            //     if (empty($redirect_url) and $payment->status == 'completed') {
            //         return redirect()
            //             ->route('user.plan')
            //             ->with($success ? 'success' : 'error', $message);
            //     }
            //     if ($payment->status == 'completed') {
            //         $redirect_url = route('user.plan');
            //     }

            //     if ($redirect_url) {
            //         return redirect()
            //             ->to($redirect_url)
            //             ->with('success', $message);
            //     }
            //     return redirect()
            //         ->route('user.plan.thank-you')
            //         ->with('success', $message);
            // } else {
            //     return redirect()
            //         ->back()
            //         ->with('error', $message);
            // }
        }
    }

    public function handleSuccessPayment(Request $request)
    {
        $orderId = $request->orderId;
        $transactionStatus = $request->transactionStatus;
        $paymentType = $request->paymentType;
        $fraudStatus = $request->fraudStatus;
        $grossAmount = $request->grossAmount;

        $user = auth()->user();
        $payment = PlanPayment::where('code', $orderId)->firstOrFail();

        if (!$payment) {
            return response()->json(['message' => 'payment not found'], 404);
        }

        if ($transactionStatus == 'capture') {
            if ($paymentType == 'credit_card') {
                $payment->status = ($fraudStatus == 'challenge') ? 'pending' : 'paid';
            }
        } elseif ($transactionStatus == 'settlement') {
            $payment->status = 'completed';


            $plan = UserPlan::query()->where('user_id', $user->id)
                ->where('plan_id', $payment->object_id)
                ->where('price', $payment->amount)
                ->orderBy('created_at', 'desc')
                ->first();  

            if ($plan) {
                $plan->status = '1';
                $plan->referal_user_id = $user->affiliate_plan_user_id;
                $plan->referal_amount = $plan->price * 0.1;
                $plan->save();
            }
        } elseif ($transactionStatus == 'pending') {
            $payment->status = 'pending';
        } elseif ($transactionStatus == 'deny') {
            $payment->status = 'failed';
        } elseif ($transactionStatus == 'expire') {
            $payment->status = 'expired';
        } elseif ($transactionStatus == 'cancel') {
            $payment->status = 'canceled';
        }

        $payment->save();

        return response()->json(['message' => 'Booking status updated']);
    }



    public function thankYou(Request $request)
    {
        return view('User::frontend.plan.thankyou');
    }

    public function planStatus()
    {
        return view('plan-status');
    }

    public function confirmPlan($code)
    {
        $payment = PlanPayment::where('code', $code)->firstOrFail();


        return view('User::frontend.plan.confirm-plan', [
            'payment' => $payment,
            'snapToken' => $payment->getMeta('snap_token')
        ]);
    }
}
