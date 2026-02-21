<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;

class VendorDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Vendor Dashboard Analytics Page.
     */
    public function index(Request $request)
    {
        $user_id = Auth::id();

        // 1. Get Top Cards Report (Pending, Earnings, Bookings, Services)
        $cardsReport = Booking::getTopCardsReportForVendor($user_id);

        $totalPending = 0;
        $totalEarnings = 0;
        $totalBookings = 0;
        $totalServices = 0;

        foreach ($cardsReport as $card) {
            if ($card['title'] === __('Pending')) {
                $totalPending = $card['amount'];
            } elseif ($card['title'] === __('Earnings')) {
                $totalEarnings = $card['amount'];
            } elseif ($card['title'] === __('Bookings')) {
                $totalBookings = $card['amount'];
            } elseif ($card['title'] === __('Services')) {
                $totalServices = $card['amount'];
            }
        }

        // 2. Get Chart Data (Earning Statistics)
        // Default: Last 7 days
        $filter = $request->input('chart_filter', 'last_7_days');
        $from = $request->input('from'); // Custom date format: Y-m-d
        $to = $request->input('to');     // Custom date format: Y-m-d

        $dateRange = $this->getDateRangeFromFilter($filter, $from, $to);

        $chartData = Booking::getEarningChartDataForVendor($dateRange['from'], $dateRange['to'], $user_id);

        // Format chart data for frontend
        $labels = $chartData['labels'];
        $earnings = $chartData['datasets'][0]['data']; // Index 0 is Total Earning
        $data = [
            'page_title' => __('Vendor Dashboard'),
            'analytics' => [
                'total_pending' => $totalPending,
                'total_earnings' => $totalEarnings,
                'total_bookings' => $totalBookings,
                'total_services' => $totalServices,
            ],
            'chart' => [
                'filter' => $filter,
                'from_date' => date('Y-m-d', $dateRange['from']),
                'to_date' => date('Y-m-d', $dateRange['to']),
                'labels' => $labels,
                'earnings' => $earnings,
            ],
        ];

        // If request is AJAX, return JSON for the chart updates
        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'chart' => $data['chart']
            ]);
        }

        return view('v2.vendor.dashboard.index', $data);
    }

    /**
     * Helper to get start and end timestamps based on filter type.
     */
    private function getDateRangeFromFilter($filter, $customFrom, $customTo)
    {
        $now = time();
        $todayStart = strtotime('today');
        $todayEnd = strtotime('tomorrow') - 1;

        switch ($filter) {
            case 'today':
                return ['from' => $todayStart, 'to' => $todayEnd];
            case 'yesterday':
                return ['from' => strtotime('yesterday'), 'to' => $todayStart - 1];
            case 'this_week':
                return ['from' => strtotime('monday this week'), 'to' => $now];
            case 'last_30_days':
                return ['from' => strtotime('-30 days'), 'to' => $now];
            case 'this_month':
                return ['from' => strtotime('first day of this month'), 'to' => $now];
            case 'last_month':
                return [
                    'from' => strtotime('first day of last month'),
                    'to' => strtotime('last day of last month 23:59:59')
                ];
            case 'this_year':
                return ['from' => strtotime('first day of January this year'), 'to' => $now];
            case 'custom_range':
                if ($customFrom && $customTo) {
                    return [
                        'from' => strtotime($customFrom . ' 00:00:00'),
                        'to' => strtotime($customTo . ' 23:59:59')
                    ];
                }
                // Fallback to last 7 days if invalid custom range
                return ['from' => strtotime('-7 days'), 'to' => $now];
            case 'last_7_days':
            default:
                return ['from' => strtotime('-7 days'), 'to' => $now];
        }
    }
}
