<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Enquiry;
use Modules\Booking\Models\EnquiryReply;
use Modules\Booking\Events\EnquiryReplyCreated;

class VendorEnquiryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Enquiry Report Table
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = Enquiry::query()
            ->where('vendor_id', $userId)
            ->whereIn('object_model', array_keys(get_bookable_services()))
            ->withCount('replies');

        // Allow searching (Filter search by Name or ID)
        $search = $request->input('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'LIKE', '%' . $search . '%')
                    ->orWhere('name', 'LIKE', '%' . $search . '%');
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
            case 'most_replies':
                $query->orderBy('replies_count', 'desc');
                break;
            case 'least_replies':
                $query->orderBy('replies_count', 'asc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $enquiries = $query->paginate(10)->appends($request->query());

        // Format for frontend
        $enquiries->getCollection()->transform(function ($enquiry) {
            $service = $enquiry->service;

            return [
                'id' => '#' . $enquiry->id,
                'raw_id' => $enquiry->id,
                'service_title' => $service ? $service->title : 'N/A',
                'customer_name' => $enquiry->name,
                'date' => $enquiry->created_at->format('M d, Y'),
                'replies_count' => $enquiry->replies_count,
                'status' => $enquiry->status,
                'status_name' => ucfirst($enquiry->status),
                'detail_url' => route('enquiry.reply', ['id' => $enquiry->id]),
            ];
        });

        $data = [
            'page_title' => __('Enquiry Report'),
            'enquiries' => $enquiries,
            'filters' => [
                'sort' => $sort,
                'status' => $status,
                'search' => $search,
            ],
            'statuses' => Enquiry::$enquiryStatus ?? ['pending', 'completed', 'cancel'],
        ];

        return view('v2.vendor.enquiry.index', $data);
    }

    /**
     * Detail Enquiry & Reply Page
     */
    public function reply(Request $request, $id)
    {
        $userId = Auth::id();
        $enquiry = Enquiry::query()
            ->where('vendor_id', $userId)
            ->where('id', $id)
            ->firstOrFail();

        $replies = $enquiry->replies()->orderByDesc('created_at')->get();

        $data = [
            'page_title' => __('Reply Enquiry Report #') . $enquiry->id,
            'enquiry' => [
                'id' => $enquiry->id,
                'name' => $enquiry->name,
                'email' => $enquiry->email,
                'phone' => $enquiry->phone,
                'note' => $enquiry->note,
            ],
            // Format replies for easy iteration in blade
            'replies' => $replies->map(function ($reply) {
                return [
                    'id' => $reply->id,
                    'user_name' => $reply->user ? $reply->user->display_name : 'System/Guest',
                    'user_avatar' => $reply->user ? $reply->user->getAvatarUrl() : null,
                    'content' => $reply->content,
                    'created_at' => $reply->created_at->format('M d, Y'), // e.g., Dec 21, 2025
                ];
            }),
            'submit_url' => route('enquiry.reply.store', ['id' => $enquiry->id])
        ];

        return view('v2.vendor.enquiry.reply', $data);
    }

    /**
     * Store new reply
     */
    public function storeReply(Request $request, $id)
    {
        $userId = Auth::id();
        $enquiry = Enquiry::query()
            ->where('vendor_id', $userId)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'content' => 'required|string'
        ]);

        $reply = new EnquiryReply();
        $reply->content = $request->input('content');
        $reply->parent_id = $enquiry->id;
        $reply->user_id = $userId;
        $reply->save();

        EnquiryReplyCreated::dispatch($reply, $enquiry);

        // Can return JSON if frontend uses AJAX, or redirect
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => true,
                'message' => __('Reply added successfully'),
                'data' => [
                    'user_name' => Auth::user()->display_name,
                    'user_avatar' => Auth::user()->getAvatarUrl(),
                    'content' => $reply->content,
                    'created_at' => $reply->created_at->format('M d, Y')
                ]
            ]);
        }

        return back()->with('success', __("Reply added successfully"));
    }
}
