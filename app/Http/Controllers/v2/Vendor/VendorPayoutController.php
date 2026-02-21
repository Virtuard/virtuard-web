<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Vendor\Events\PayoutRequestEvent;
use Modules\Vendor\Models\VendorPayout;

class VendorPayoutController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Vendor Payout Page (V2)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Calculations for header section
        $availableBalance = $user->available_payout_amount;
        $totalPending = VendorPayout::query()
            ->where('vendor_id', $user->id)
            ->whereIn('status', ['initial', 'confirmed'])
            ->sum('amount');
        $totalPaid = $user->total_paid;

        // Payout methods (For setup / request modals)
        $availableMethods = $user->available_payout_methods;
        $payoutAccounts = $user->getMeta('vendor_payout_accounts');
        if ($payoutAccounts) {
            $payoutAccounts = json_decode($payoutAccounts, true);
        } else {
            $payoutAccounts = [];
        }

        // Available methods from settings
        $allPayoutMethods = json_decode(setting_item('vendor_payout_methods'), true) ?? [];

        $data = [
            'page_title' => __('Vendor Payout'),
            'summary' => [
                'available_balance' => format_money($availableBalance),
                'total_pending' => format_money($totalPending),
                'total_paid' => format_money($totalPaid),
                'raw_available_balance' => $availableBalance
            ],
            'payout_accounts' => $payoutAccounts,
            'all_methods' => $allPayoutMethods,
            'setup_account_action' => route('vendor2.payout.setup'),
            'request_payout_action' => route('vendor2.payout.request'),
        ];

        return view('v2.vendor.payout.index', $data);
    }

    /**
     * Save / Set up Payout Account Details
     */
    public function setupAccount(Request $request)
    {
        $user = Auth::user();

        $user->addMeta('vendor_payout_accounts', json_encode($request->input('payout_accounts', [])));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => __("Your account information has been saved")
            ]);
        }

        return back()->with('success', __("Your account information has been saved"));
    }

    /**
     * Request a new Payout
     */
    public function requestPayout(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $user->available_payout_amount,
            'payout_method' => 'required|string'
        ]);

        $amount = $request->input('amount');
        $payout_method = $request->input('payout_method');

        $user_available_methods = $user->available_payout_methods;

        // Check if the user has set up their account info for the requested method
        if (empty($user_available_methods) || empty($user_available_methods[$payout_method])) {
            return $this->sendError(__("You have not set up the account info for this payout method."));
        }

        if ($user->available_payout_amount < $amount) {
            return $this->sendError(__("You do not have enough available balance for this payout."));
        }

        $method_detail = (object) $user_available_methods[$payout_method];

        if (isset($method_detail->min) && $method_detail->min > $amount) {
            return $this->sendError(__("Minimum amount to pay is :amount", ["amount" => format_money($method_detail->min)]));
        }

        // Create Payout Request
        $payout = new VendorPayout();
        $payout->payout_method = $payout_method;
        $payout->amount = $amount;
        $payout->note_to_admin = $request->input('note', '');
        $payout->account_info = $method_detail->user ?? ''; // the string info saved in setupAccount
        $payout->vendor_id = $user->id;
        $payout->status = 'initial';

        if ($payout->save()) {
            event(new PayoutRequestEvent('insert', $payout));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'status' => true,
                    'message' => __("Payout request has been created")
                ]);
            }
            return back()->with('success', __("Payout request has been created"));
        } else {
            return $this->sendError(__("Cannot create payout request at this time."));
        }
    }

    /**
     * Helper to return consistent error response
     */
    public function sendError($message, $data = [])
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'status' => false,
                'message' => $message,
                'data' => $data
            ], 400);
        }
        return back()->with('error', $message);
    }
}
