<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Booking;

class VendorBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Vendor Booking History Page with Filters
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Booking::query()
            ->where('vendor_id', $userId)
            ->where('status', '!=', 'draft');

        // Allow searching (Filter search by ID or listing title)
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('title', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        // Filter by Listing Type (Accomodation/hotel, Property/space, Business/business)
        $listingType = $request->input('listing', 'all');
        if ($listingType !== 'all') {
            $query->where('object_model', $listingType);
        }

        // Filter by Status
        $status = $request->input('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Sort By
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('total', 'desc');
                break;
            case 'lowest':
                $query->orderBy('total', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $bookings = $query->paginate(10)->appends($request->query());

        // Format for frontend
        $bookings->through(function ($booking) {
            $service = $booking->service;

            return [
                'id' => '#' . $booking->id,
                'raw_id' => $booking->id,
                'type' => ucfirst($booking->object_model), // Accomodation, Property, etc.
                'listing_title' => $service ? $service->title : 'N/A',
                'listing_url' => $service ? $service->getDetailUrl() : '#',
                'order_date' => $booking->created_at->format('M d, Y'),
                'execution_time' => [
                    'check_in' => $booking->start_date ? date('m/d/Y', strtotime($booking->start_date)) : null,
                    'check_out' => $booking->end_date ? date('m/d/Y', strtotime($booking->end_date)) : null,
                    'duration' => $booking->duration_nights . ' nights'
                ],
                'total' => format_money($booking->total),
                'paid' => format_money($booking->paid),
                'remain' => format_money($booking->total - $booking->paid),
                'status' => $booking->status, // e.g., completed, processing, confirmed, cancelled, paid, unpaid, partial_payment
                'status_name' => $booking->statusName,
                'invoice_url' => route('user.booking.invoice', ['code' => $booking->code]),
            ];
        });

        $data = [
            'page_title' => __('Booking History'),
            'bookings' => $bookings,
            'filters' => [
                'sort' => $sort,
                'listing' => $listingType,
                'status' => $status,
                'search' => $search,
            ],
            // For filter UI dropdowns
            'listing_types' => [
                'all' => __('All Listing'),
                'hotel' => __('Accommodation'),
                'space' => __('Property'),
                'business' => __('Business')
            ],
            'statuses' => array_keys(config('booking.statuses', []))
        ];

        return view('v2.vendor.booking.index', $data);
    }
    /**
     * Booking Detail Page
     */
    public function detail(Request $request, $id)
    {
        $userId = Auth::id();
        $booking = Booking::query()
            ->where('vendor_id', $userId)
            ->where('id', $id)
            ->firstOrFail();

        $service = $booking->service;
        $vendor = $booking->vendor;

        $data = [
            'page_title' => __('Booking Detail #') . $booking->id,
            'booking' => $booking,
            'service' => $service,
            'vendor' => $vendor,
            'summary' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'status_name' => $booking->statusName,
                'order_date' => $booking->created_at->format('m/d/Y'),
                'vendor_name' => $service ? $service->title : 'N/A',
                'check_in' => $booking->start_date ? date('m/d/Y', strtotime($booking->start_date)) : null,
                'check_out' => $booking->end_date ? date('m/d/Y', strtotime($booking->end_date)) : null,
                'nights' => $booking->duration_nights,
                'adults' => $booking->total_guests,
                'total' => format_money($booking->total),
                'paid' => format_money($booking->paid),
                'remain' => format_money($booking->total - $booking->paid),
            ],
            'personal_info' => [
                'first_name' => $booking->first_name,
                'last_name' => $booking->last_name,
                'email' => $booking->email,
                'phone' => $booking->phone,
                'address' => $booking->address,
                'address2' => $booking->address2,
                'city' => $booking->city,
                'state' => $booking->state,
                'country' => get_country_name($booking->country),
                'zip_code' => $booking->zip_code,
            ],
        ];

        return view('v2.vendor.booking.detail', $data);
    }

    /**
     * Printable Invoice Page
     */
    public function invoice(Request $request, $id)
    {
        $userId = Auth::id();
        $booking = Booking::query()
            ->where('vendor_id', $userId)
            ->where('id', $id)
            ->firstOrFail();

        $service = $booking->service;

        $rooms = $booking->number ? $booking->number : 1;
        $bed = ($service && isset($service->bed)) ? $service->bed : 1;
        $bathroom = ($service && isset($service->bathroom)) ? $service->bathroom : 1;

        $data = [
            'page_title' => __('Invoice #') . $booking->id,
            'booking' => $booking,
            'service' => $service,
            'invoice' => [
                'id' => $booking->id,
                'created_at' => display_date($booking->created_at),
                'booking_id_transaction' => rand(10000000000, 99999999999),
                'booking_code' => $booking->code,
                'customer_name' => $booking->first_name . ' ' . $booking->last_name,
                'customer_email' => $booking->email,
                'customer_phone' => $booking->phone,
                'service_title' => $service ? $service->title : 'N/A',
                'service_address' => $service ? $service->address : '',
                'rooms' => $rooms,
                'bed' => $bed,
                'bathroom' => $bathroom,
                'nights' => $booking->duration_nights,
                'adults' => $booking->total_guests,
                'status' => $booking->statusName,
                'check_in' => display_date($booking->start_date),
                'check_out' => display_date($booking->end_date),
                'total' => format_money($booking->total),
                'paid' => format_money($booking->paid),
                'remain' => format_money($booking->total - $booking->paid),
            ]
        ];

        return view('v2.vendor.booking.invoice', $data);
    }

    /**
     * Booking Report Page
     */
    public function reportIndex(Request $request)
    {
        $userId = Auth::id();
        $query = Booking::query()
            ->where('vendor_id', $userId)
            ->where('status', '!=', 'draft');

        // Allow searching
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', '%' . $search . '%')
                    ->orWhereHas('service', function ($serviceQuery) use ($search) {
                        $serviceQuery->where('title', 'LIKE', '%' . $search . '%');
                    });
            });
        }

        // Filter by Status
        $status = $request->input('status', 'all');
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Sort By
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'highest':
                $query->orderBy('total', 'desc');
                break;
            case 'lowest':
                $query->orderBy('total', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $bookings = $query->paginate(10)->appends($request->query());

        // Format for frontend
        $bookings->through(function ($booking) {
            $service = $booking->service;
            $vendorEarning = $booking->total - $booking->commission;

            return [
                'id' => '#' . $booking->id,
                'raw_id' => $booking->id,
                'listing_title' => $service ? $service->title : 'N/A',
                'listing_url' => $service ? $service->getDetailUrl() : '#',
                'customer_name' => $booking->first_name . ' ' . $booking->last_name,
                'stay_date' => $booking->start_date ? date('M d, Y', strtotime($booking->start_date)) : '',
                'total' => format_money($booking->total),
                'paid' => format_money($booking->paid),
                'remain' => format_money($booking->total - $booking->paid),
                'vendor_earning' => format_money($vendorEarning),
                'status' => $booking->status,
                'status_name' => collect(config('booking.statuses'))->get($booking->status) ?? ucfirst($booking->status),
                'detail_url' => route('booking.report.detail', ['id' => $booking->id]),
            ];
        });

        $data = [
            'page_title' => __('Booking Report'),
            'bookings' => $bookings,
            'filters' => [
                'sort' => $sort,
                'status' => $status,
                'search' => $search,
            ],
            'statuses' => array_keys(config('booking.statuses', []))
        ];

        return view('v2.vendor.booking-report.index', $data);
    }

    /**
     * Booking Report Detail Page
     */
    public function reportDetail(Request $request, $id)
    {
        // Re-use detail logic but return a different view
        $userId = Auth::id();
        $booking = Booking::query()
            ->where('vendor_id', $userId)
            ->where('id', $id)
            ->firstOrFail();

        $service = $booking->service;
        $vendor = $booking->vendor;

        $data = [
            'page_title' => __('Detail Booking Report #') . $booking->id,
            'booking' => $booking,
            'service' => $service,
            'vendor' => $vendor,
            'summary' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'status_name' => collect(config('booking.statuses'))->get($booking->status) ?? ucfirst($booking->status),
                'order_date' => $booking->created_at->format('m/d/Y'),
                'vendor_name' => $service ? $service->title : 'N/A',
                'check_in' => $booking->start_date ? date('m/d/Y', strtotime($booking->start_date)) : null,
                'check_out' => $booking->end_date ? date('m/d/Y', strtotime($booking->end_date)) : null,
                'nights' => $booking->duration_nights,
                'adults' => $booking->total_guests,
                'total' => format_money($booking->total),
                'paid' => format_money($booking->paid),
                'remain' => format_money($booking->total - $booking->paid),
            ],
            'personal_info' => [
                'first_name' => $booking->first_name,
                'last_name' => $booking->last_name,
                'email' => $booking->email,
                'phone' => $booking->phone,
                'address' => $booking->address,
                'address2' => $booking->address2,
                'city' => $booking->city,
                'state' => $booking->state,
                'country' => get_country_name($booking->country),
                'zip_code' => $booking->zip_code,
            ],
        ];

        return view('v2.vendor.booking-report.detail', $data);
    }
}
