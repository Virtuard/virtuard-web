<?php

namespace App\Http\Controllers\v2\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Business\Models\Business;
use Modules\Location\Models\Location;
use Modules\Core\Models\Terms;

class ExploreController extends Controller
{
    protected $perPage = 12;

    /**
     * Main explore page — renders view with initial data + filter options.
     */
    public function index(Request $request)
    {
        $listings = $this->getListings($request);
        $filterOptions = $this->getFilterOptions();

        $data = [
            'page_title' => __('Explore'),
            'listings' => $listings,
            'filters' => $filterOptions,
            'currentFilters' => $this->getCurrentFilters($request),
        ];

        return view('v2.explore.index', $data);
    }

    /**
     * AJAX endpoint — returns filtered listings as JSON.
     */
    public function search(Request $request)
    {
        $listings = $this->getListings($request);

        return response()->json([
            'status' => true,
            'data' => $listings->items(),
            'pagination' => [
                'current_page' => $listings->currentPage(),
                'last_page' => $listings->lastPage(),
                'per_page' => $listings->perPage(),
                'total' => $listings->total(),
            ],
        ]);
    }

    /**
     * AJAX endpoint — returns map markers as JSON.
     */
    public function mapData(Request $request)
    {
        $req = $request->all();
        $mapListings = [];
        $models = $this->getModelsByType($req['service_type'] ?? 'all');

        foreach ($models as $key => $model) {
            $results = $model->search($req)->get();
            foreach ($results as $item) {
                if (!$item->map_lat || !$item->map_lng)
                    continue;
                $mapListings[] = get_map_listing($key, $item);
            }
        }

        return response()->json([
            'status' => true,
            'data' => $mapListings,
        ]);
    }

    /**
     * Get paginated listings based on filters.
     */
    private function getListings(Request $request)
    {
        $req = $request->all();
        $serviceType = $req['service_type'] ?? 'all';
        $sortBy = $req['sort_by'] ?? 'latest';

        $models = $this->getModelsByType($serviceType);
        $allItems = collect();

        foreach ($models as $key => $model) {
            $query = $model->search($req);
            $results = $query->get();

            foreach ($results as $item) {
                $review = $item->getScoreReview();
                $allItems->push([
                    'id' => $item->id,
                    'type' => $key,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'url' => $item->getDetailUrl(false),
                    'image' => $item->getImageUrl(),
                    'location_name' => $item->location ? $item->location->name : null,
                    'price' => $item->price,
                    'sale_price' => $item->sale_price,
                    'price_html' => format_money($item->sale_price ?: $item->price),
                    'bed' => $item->bed ?? null,
                    'bathroom' => $item->bathroom ?? null,
                    'review_score' => $review['score_total'],
                    'total_review' => $review['total_review'],
                    'is_wishlist' => $item->isWishList(),
                    'map_lat' => $item->map_lat,
                    'map_lng' => $item->map_lng,
                    'has_360' => !empty($item->ipanorama_url),
                    'created_at' => $item->created_at,
                ]);
            }
        }

        // Sort
        $sorted = match ($sortBy) {
            'top_rated' => $allItems->sort(function ($a, $b) {
                    if ($b['review_score'] != $a['review_score']) {
                        return $b['review_score'] <=> $a['review_score'];
                    }
                    return $b['total_review'] <=> $a['total_review'];
                }),
            'random' => $allItems->shuffle(),
            default => $allItems->sortByDesc('created_at'), // latest
        };

        // Manual pagination
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $this->perPage;
        $paginatedItems = $sorted->slice($offset, $this->perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            $sorted->count(),
            $this->perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );
    }

    /**
     * Get model instances based on service type filter.
     */
    private function getModelsByType(string $type): array
    {
        return match ($type) {
            'hotel' => ['hotel' => new Hotel()],
            'space' => ['space' => new Space()],
            'business' => ['business' => new Business()],
            default => [
                'hotel' => new Hotel(),
                'space' => new Space(),
                'business' => new Business(),
            ],
        };
    }

    /**
     * Get all filter options for sidebar.
     */
    private function getFilterOptions(): array
    {
        // Locations
        $locations = Location::where('status', 'publish')
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();

        // Accommodation types (Hotel terms/attributes)
        $accommodationTypes = $this->getTermsByService('hotel');

        // Business types
        $businessTypes = $this->getTermsByService('business');

        // Rent types (Space terms/attributes)
        $rentTypes = $this->getTermsByService('space');

        return [
            'locations' => $locations,
            'accommodation_types' => $accommodationTypes,
            'business_types' => $businessTypes,
            'rent_types' => $rentTypes,
            'service_types' => [
                ['value' => 'all', 'label' => __('All Listings')],
                ['value' => 'hotel', 'label' => __('Accommodation')],
                ['value' => 'space', 'label' => __('Property')],
                ['value' => 'business', 'label' => __('Commercial Activities')],
            ],
            'sort_options' => [
                ['value' => 'latest', 'label' => __('Latest')],
                ['value' => 'top_rated', 'label' => __('Top Rated')],
                ['value' => 'random', 'label' => __('Random')],
            ],
        ];
    }

    /**
     * Get attribute terms for a given service type.
     */
    private function getTermsByService(string $service): array
    {
        $attrIds = \DB::table('bravo_attrs')
            ->where('service', $service)
            ->whereNull('deleted_at')
            ->pluck('id');

        if ($attrIds->isEmpty())
            return [];

        return Terms::whereIn('attr_id', $attrIds)
            ->orderBy('name')
            ->get(['id', 'name', 'attr_id'])
            ->toArray();
    }

    /**
     * Extract current active filters from request for frontend state.
     */
    private function getCurrentFilters(Request $request): array
    {
        return [
            'service_type' => $request->input('service_type', 'all'),
            'sort_by' => $request->input('sort_by', 'latest'),
            'location_id' => $request->input('location_id'),
            'keyword' => $request->input('keyword'),
            'proximity' => $request->input('proximity'),
            'has_360' => $request->input('has_360'),
            'terms' => $request->input('terms', []),
            'page' => $request->input('page', 1),
        ];
    }
}
