<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Business\Models\Business;

class VendorBusinessController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the businesses.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Business::where('author_id', $userId);

        // Search Filter
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        // Sort Filter (newest, oldest, rating, popularity)
        switch ($request->input('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'rating':
                // Assuming review_score exists in the table or appended attribute, otherwise fallback to id or created_at
                $query->orderBy('review_score', 'desc');
                break;
            case 'popularity':
                // Popularity uses view_count
                $query->orderBy('view_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $businesses = $query->paginate(10);

        return view('v2.vendor.business.index', compact('businesses'));
    }

    /**
     * Show the form for creating a new business.
     */
    public function create()
    {
        $row = new Business();
        $row->fill([
            'status' => 'publish',
        ]);

        return view('v2.vendor.business.add', compact('row'));
    }

    /**
     * Store or Update business with database-backed validation.
     */
    public function store(Request $request, $id = null)
    {
        $userId = Auth::id();

        // 1. Validation Rules (Mapping to `bravo_businesses` table schema based on Business model mapping)
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'video' => 'nullable|string|url',
            'franchising' => 'nullable|string|max:255',
            // Location
            'address' => 'nullable|string|max:255',
            'map_lat' => 'nullable|string|max:50',
            'map_lng' => 'nullable|string|max:50',
            'map_zoom' => 'nullable|integer',
            // Attributes & Info
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|url|max:255',
            // Content/Virtual
            'ipanorama_id' => 'nullable|integer|exists:ipanorama,id',
            // Ical / SEO / Custom JSON structures like FAQs and Items (Products)
            'seo_title' => 'nullable|string|max:255',
            'seo_desc' => 'nullable|string',
            'seo_share' => 'nullable|string',
            'seo_image' => 'nullable|integer',
        ];

        $validatedData = $request->validate($rules);

        // 2. Determine Create or Update
        if ($id) {
            $row = Business::where('author_id', $userId)->findOrFail($id);
            $message = __('Business updated successfully');
        } else {
            $row = new Business();
            $row->author_id = $userId;
            $row->status = 'publish'; // Default creation status
            $message = __('Business created successfully');
        }

        // 3. Mapping fields natively based on actual business columns
        $dataKeys = [
            'title',
            'content',
            'video',
            'address',
            'map_lat',
            'map_lng',
            'map_zoom',
            'franchising',
            'phone',
            'website',
            'ipanorama_id',
            'seo_title',
            'seo_desc',
            'seo_share',
            'seo_image'
        ];

        $input = $request->only($dataKeys);

        // Manual processing for FAQS
        if ($request->has('faqs')) {
            $row->setAttribute('faqs', is_array($request->input('faqs')) ? $request->input('faqs') : json_decode($request->input('faqs'), true));
        }

        // Manual processing for Items (Products)
        if ($request->has('items')) {
            $row->setAttribute('items', is_array($request->input('items')) ? $request->input('items') : json_decode($request->input('items'), true));
        }

        // Fill existing columns
        foreach ($input as $key => $val) {
            $row->{$key} = $val;
        }

        $row->save();

        return redirect()->route('business.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified business.
     */
    public function edit($id)
    {
        $userId = Auth::id();
        $row = Business::where('author_id', $userId)->findOrFail($id);

        return view('v2.vendor.business.edit', compact('row'));
    }

    /**
     * Display the specified business detail page.
     */
    public function show($id)
    {
        $userId = Auth::id();
        $business = Business::where('author_id', $userId)->findOrFail($id);

        return view('v2.vendor.business.show', compact('business'));
    }

    /**
     * Update the status of the specified business.
     */
    public function updateStatus(Request $request, $id)
    {
        $userId = Auth::id();
        $action = $request->input('action');

        $business = Business::where('author_id', $userId)->where('id', $id)->firstOrFail();

        if (in_array($action, ['publish', 'draft', 'unlisted'])) {
            $business->status = $action;
            $business->save();
            return redirect()->back()->with('success', __('Status updated successfully'));
        }

        return redirect()->back()->with('error', __('Invalid status'));
    }

    /**
     * Remove the specified business from storage.
     */
    public function delete($id)
    {
        $userId = Auth::id();
        $business = Business::where('author_id', $userId)->where('id', $id)->firstOrFail();

        // This uses soft deletes since Business uses SoftDeletes trait
        $business->delete();

        return redirect()->back()->with('success', __('Business deleted successfully'));
    }
}
