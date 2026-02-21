<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\User\Models\UserWishList;

class VendorWishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Vendor Wishlist Page (V2)
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        // Retrieve wishlist with loaded services
        $wishlists = UserWishList::query()
            ->with(['service'])
            ->where('user_id', $userId)
            ->get();

        $listingFilter = $request->input('listing', 'none');
        if ($listingFilter !== 'none') {
            $wishlists = $wishlists->where('object_model', $listingFilter);
        }

        /**
         * Sort By (Recently Added, Rating, Name)
         */
        $sortFilter = $request->input('sort', 'recent'); // 'recent', 'rating', 'name'
        if ($sortFilter === 'rating') {
            $wishlists = $wishlists->sortByDesc(function ($item) {
                return $item->service->review_score ?? 0;
            });
        } elseif ($sortFilter === 'name') {
            $wishlists = $wishlists->sortBy(function ($item) {
                return $item->service->title ?? '';
            }, SORT_NATURAL | SORT_FLAG_CASE);
        } else {
            $wishlists = $wishlists->sortByDesc('created_at');
        }

        /**
         * Filter Price (None, Low to High, High to Low)
         */
        $priceFilter = $request->input('price', 'none'); // 'low', 'high', 'none'
        if ($priceFilter === 'low') {
            $wishlists = $wishlists->sortBy(function ($item) {
                return $item->service->price ?? 0;
            });
        } elseif ($priceFilter === 'high') {
            $wishlists = $wishlists->sortByDesc(function ($item) {
                return $item->service->price ?? 0;
            });
        }

        // Pagination for collections
        $perPage = 12;
        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $paginatedWishlist = new \Illuminate\Pagination\LengthAwarePaginator(
            $wishlists->forPage($page, $perPage)->values(),
            $wishlists->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
        $paginatedWishlist->appends($request->query());

        // Transform for frontend
        $paginatedWishlist->through(function ($item) {
            $service = $item->service;

            return [
                'id' => $item->id, // Wishlist record ID (useful for deletion)
                'object_id' => $item->object_id,
                'object_model' => $item->object_model,
                'title' => $service->title ?? 'N/A',
                'url' => $service ? $service->getDetailUrl() : '#',
                'image_url' => $service ? get_file_url($service->image_id, 'medium') : null,
                'location' => $service->location->name ?? ($service->address ?? ''),
                'price' => format_money($service->price ?? 0),
                'price_unit' => '/ night', // You can customize this based on object_model if needed (e.g., /mo for property)
                'review_score' => $service->review_score ?? 0,
                'review_count' => $service->review_list_count ?? 0,
            ];
        });

        // Determine dynamic types present in the query (for `<select name="listing">` auto-generation)
        $availableTypes = UserWishList::query()->where('user_id', $userId)->distinct('object_model')->pluck('object_model')->toArray();
        $listingOptions = [];
        foreach ($availableTypes as $type) {
            $listingOptions[$type] = ucfirst($type); // Output: hotel -> Hotel, space -> Space
        }

        $data = [
            'page_title' => __('My Wishlist'),
            'wishlist' => $paginatedWishlist,
            'filters' => [
                'sort' => $sortFilter,
                'listing' => $listingFilter,
                'price' => $priceFilter,
            ],
            'listing_options' => $listingOptions
        ];

        return view('v2.vendor.wishlist.index', $data);
    }
}
