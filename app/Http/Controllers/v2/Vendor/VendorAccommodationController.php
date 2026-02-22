<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Hotel\Models\Hotel;

class VendorAccommodationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the accommodations.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Hotel::where('author_id', $userId);

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
                // Assuming review_score exists, if not, adjust logic
                $query->orderBy('review_score', 'desc');
                break;
            case 'popularity':
                // Popularity can be view_count or bookings count
                $query->orderBy('view_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Price Filter (none, low_to_high, high_to_low)
        switch ($request->input('price', 'none')) {
            case 'low_to_high':
                $query->orderBy('price', 'asc');
                break;
            case 'high_to_low':
                $query->orderBy('price', 'desc');
                break;
            case 'none':
            default:
                break;
        }

        $accommodations = $query->paginate(10);

        return view('v2.vendor.accommodation.index', compact('accommodations'));
    }

    /**
     * Show the form for creating a new accommodation.
     */
    public function create()
    {
        $row = new Hotel();
        $row->fill([
            'status' => 'publish',
        ]);

        return view('v2.vendor.accommodation.add', compact('row'));
    }

    /**
     * Store or Update accommodation with database-backed validation.
     */
    public function store(Request $request, $id = null)
    {
        $userId = Auth::id();

        // 1. Validation Rules (Mapping to `bravo_hotels` table schema)
        // Adjust standard length and required fields.
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'video' => 'nullable|string|url',
            // Pricing
            'price' => 'required|numeric|min:0',
            'check_in_time' => 'nullable|string|max:100',
            'check_out_time' => 'nullable|string|max:100',
            'min_day_stays' => 'nullable|integer|min:0',
            // Location
            'address' => 'nullable|string|max:255',
            'map_lat' => 'nullable|string|max:50',
            'map_lng' => 'nullable|string|max:50',
            'map_zoom' => 'nullable|integer',
            // Attributes & Info
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|url|max:255',
            'chain' => 'nullable|string|max:150',
            'star_rate' => 'nullable|integer|min:1|max:5',
            // Content/Virtual
            'ipanorama_id' => 'nullable|integer|exists:ipanorama,id',
            // Ical / SEO
            'ical_import_url' => 'nullable|string|url',
            'seo_title' => 'nullable|string|max:255',
            'seo_desc' => 'nullable|string',
            'seo_share' => 'nullable|string',
            'seo_image' => 'nullable|integer', // Assuming image ID format for Bravo CMS
        ];

        $validatedData = $request->validate($rules);

        // 2. Determine Create or Update
        if ($id) {
            $row = Hotel::where('author_id', $userId)->findOrFail($id);
            $message = __('Accommodation updated successfully');
        } else {
            $row = new Hotel();
            $row->author_id = $userId;
            $row->status = 'publish'; // Default creation status
            $message = __('Accommodation created successfully');
        }

        // 3. Mapping fields natively based on actual `bravo_hotels` columns
        $dataKeys = [
            'title',
            'content',
            'video',
            'address',
            'map_lat',
            'map_lng',
            'map_zoom',
            'price',
            'check_in_time',
            'check_out_time',
            'min_day_stays',
            'chain',
            'phone',
            'website',
            'star_rate',
            'ipanorama_id',
            'ical_import_url',
            'seo_title',
            'seo_desc',
            'seo_share',
            'seo_image'
        ];

        // Ensure we only retrieve mapped keys safely
        $input = $request->only($dataKeys);

        // Advanced mapping placeholders (Assuming standard boolean mappings for checkboxes)
        $row->enable_extra_price = $request->input('enable_extra_price') == 'on' ? 1 : 0;
        $row->enable_service_fee = $request->input('enable_service_fee') == 'on' ? 1 : 0;

        // Fill existing columns
        foreach ($input as $key => $val) {
            $row->{$key} = $val;
        }

        $row->save();

        // (Optional: handle term associations for tab-attributes & term_id here -> $row->terms()->sync(...))

        return redirect()->route('accommodation.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified accommodation.
     */
    public function edit($id)
    {
        $userId = Auth::id();
        $row = Hotel::where('author_id', $userId)->findOrFail($id);

        return view('v2.vendor.accommodation.edit', compact('row'));
    }

    /**
     * Display the specified accommodation detail page.
     */
    public function show($id)
    {
        $userId = Auth::id();
        $hotel = Hotel::where('author_id', $userId)->findOrFail($id);

        return view('v2.vendor.accommodation.show', compact('hotel'));
    }

    /**
     * Update the status of the specified accommodation.
     */
    public function updateStatus(Request $request, $id)
    {
        $userId = Auth::id();
        $action = $request->input('action');

        $hotel = Hotel::where('author_id', $userId)->where('id', $id)->firstOrFail();

        if (in_array($action, ['publish', 'draft', 'unlisted'])) {
            $hotel->status = $action;
            $hotel->save();
            return redirect()->back()->with('success', __('Status updated successfully'));
        }

        return redirect()->back()->with('error', __('Invalid status'));
    }

    /**
     * Remove the specified accommodation from storage.
     */
    public function delete($id)
    {
        $userId = Auth::id();
        $hotel = Hotel::where('author_id', $userId)->where('id', $id)->firstOrFail();

        // This usually uses soft deletes since Hotel uses SoftDeletes trait
        $hotel->delete();

        return redirect()->back()->with('success', __('Accommodation deleted successfully'));
    }
}
