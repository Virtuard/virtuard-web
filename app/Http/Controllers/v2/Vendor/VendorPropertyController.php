<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Space\Models\Space;

class VendorPropertyController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of the properties.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Space::where('author_id', $userId);

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

        $properties = $query->paginate(10);

        return view('v2.vendor.property.index', compact('properties'));
    }

    /**
     * Show the form for creating a new property.
     */
    public function create()
    {
        $row = new Space();
        $row->fill([
            'status' => 'publish',
        ]);

        return view('v2.vendor.property.add', compact('row'));
    }

    /**
     * Store or Update property with database-backed validation.
     */
    public function store(Request $request, $id = null)
    {
        $userId = Auth::id();

        // 1. Validation Rules (Mapping to `bravo_spaces` table schema based on Space model mapping)
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'video' => 'nullable|string|url',
            // Properties specifically requested in mockups matching Space
            'bed' => 'nullable|integer',
            'bathroom' => 'nullable|integer',
            'flooring' => 'nullable|integer',
            'square_land' => 'nullable|numeric',
            'square' => 'nullable|numeric', // Built square meters
            'agency' => 'nullable|string|max:255',
            'land_registry_category' => 'nullable|string|max:255',
            // Pricing
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            // Location
            'address' => 'nullable|string|max:255',
            'map_lat' => 'nullable|string|max:50',
            'map_lng' => 'nullable|string|max:50',
            'map_zoom' => 'nullable|integer',
            // Attributes & Info
            'phone' => 'nullable|string|max:50',
            'website' => 'nullable|string|url|max:255',
            'star_rate' => 'nullable|integer|min:1|max:5',
            // Content/Virtual
            'ipanorama_id' => 'nullable|integer|exists:ipanorama,id',
            // Ical / SEO / Custom JSON structures like FAQs
            'ical_import_url' => 'nullable|string|url',
            'seo_title' => 'nullable|string|max:255',
            'seo_desc' => 'nullable|string',
            'seo_share' => 'nullable|string',
            'seo_image' => 'nullable|integer',
        ];

        $validatedData = $request->validate($rules);

        // 2. Determine Create or Update
        if ($id) {
            $row = Space::where('author_id', $userId)->findOrFail($id);
            $message = __('Property updated successfully');
        } else {
            $row = new Space();
            $row->author_id = $userId;
            $row->status = 'publish'; // Default creation status
            $message = __('Property created successfully');
        }

        // 3. Mapping fields natively based on actual space columns
        $dataKeys = [
            'title',
            'content',
            'video',
            'address',
            'map_lat',
            'map_lng',
            'map_zoom',
            'price',
            'sale_price',
            'bed',
            'bathroom',
            'flooring',
            'square_land',
            'square',
            'agency',
            'land_registry_category',
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

        $input = $request->only($dataKeys);

        // Manual processing for FAQS (assuming frontend sends arrays or JSON strings)
        if ($request->has('faqs')) {
            // Add custom validation or sanitize for FAQs here
            $row->faqs = $request->input('faqs'); // Must be cast to array in model
        }

        // Fill existing columns
        foreach ($input as $key => $val) {
            $row->{$key} = $val;
        }

        $row->save();

        return redirect()->route('property.index')->with('success', $message);
    }

    /**
     * Show the form for editing the specified property.
     */
    public function edit($id)
    {
        $userId = Auth::id();
        $row = Space::where('author_id', $userId)->findOrFail($id);

        return view('v2.vendor.property.edit', compact('row'));
    }

    /**
     * Display the specified property detail page.
     */
    public function show($id)
    {
        $userId = Auth::id();
        $property = Space::where('author_id', $userId)->findOrFail($id);

        return view('v2.vendor.property.show', compact('property'));
    }

    /**
     * Update the status of the specified property.
     */
    public function updateStatus(Request $request, $id)
    {
        $userId = Auth::id();
        $action = $request->input('action');

        $property = Space::where('author_id', $userId)->where('id', $id)->firstOrFail();

        if (in_array($action, ['publish', 'draft', 'unlisted'])) {
            $property->status = $action;
            $property->save();
            return redirect()->back()->with('success', __('Status updated successfully'));
        }

        return redirect()->back()->with('error', __('Invalid status'));
    }

    /**
     * Remove the specified property from storage.
     */
    public function delete($id)
    {
        $userId = Auth::id();
        $property = Space::where('author_id', $userId)->where('id', $id)->firstOrFail();

        // This uses soft deletes since Space uses SoftDeletes trait
        $property->delete();

        return redirect()->back()->with('success', __('Property deleted successfully'));
    }
}
