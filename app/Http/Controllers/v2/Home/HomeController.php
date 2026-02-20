<?php

namespace App\Http\Controllers\v2\Home;

use App\Http\Controllers\Controller;
use App\User;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Business\Models\Business;
use Modules\User\Models\Plan;

class HomeController extends Controller
{
    public function index()
    {
        $categories = $this->getSpaceCategories();

        $accommodations = Hotel::where('status', 'publish')
            ->with(['location', 'hasWishList'])
            ->limit(20)
            ->get()
            ->map(function ($hotel) {
                $review = $hotel->getScoreReview();
                return [
                    'id' => $hotel->id,
                    'title' => $hotel->title,
                    'slug' => $hotel->slug,
                    'url' => $hotel->getDetailUrl(false),
                    'image' => $hotel->getImageUrl(),
                    'location_name' => $hotel->location ? $hotel->location->name : null,
                    'price' => $hotel->price,
                    'sale_price' => $hotel->sale_price,
                    'price_html' => format_money($hotel->sale_price ?: $hotel->price),
                    'bed' => $hotel->bed,
                    'bathroom' => $hotel->bathroom,
                    'review_score' => $review['score_total'],
                    'total_review' => $review['total_review'],
                    'is_wishlist' => $hotel->isWishList(),
                ];
            })
            ->sort(function ($a, $b) {
                if ($b['review_score'] != $a['review_score']) {
                    return $b['review_score'] <=> $a['review_score'];
                }
                return $b['total_review'] <=> $a['total_review'];
            })
            ->take(6)
            ->values();

        $properties = Space::where('status', 'publish')
            ->with(['location', 'hasWishList'])
            ->limit(20)
            ->get()
            ->map(function ($space) {
                $review = $space->getScoreReview();
                return [
                    'id' => $space->id,
                    'title' => $space->title,
                    'slug' => $space->slug,
                    'url' => $space->getDetailUrl(false),
                    'image' => $space->getImageUrl(),
                    'location_name' => $space->location ? $space->location->name : null,
                    'price' => $space->price,
                    'sale_price' => $space->sale_price,
                    'price_html' => format_money($space->sale_price ?: $space->price),
                    'review_score' => $review['score_total'],
                    'total_review' => $review['total_review'],
                    'is_wishlist' => $space->isWishList(),
                ];
            })
            ->sort(function ($a, $b) {
                if ($b['review_score'] != $a['review_score']) {
                    return $b['review_score'] <=> $a['review_score'];
                }
                return $b['total_review'] <=> $a['total_review'];
            })
            ->take(6)
            ->values();

        $businesses = Business::where('status', 'publish')
            ->with(['location', 'hasWishList'])
            ->limit(20)
            ->get()
            ->map(function ($business) {
                $review = $business->getScoreReview();
                return [
                    'id' => $business->id,
                    'title' => $business->title,
                    'slug' => $business->slug,
                    'url' => $business->getDetailUrl(false),
                    'image' => $business->getImageUrl(),
                    'location_name' => $business->location ? $business->location->name : null,
                    'price' => $business->price,
                    'sale_price' => $business->sale_price,
                    'price_html' => format_money($business->sale_price ?: $business->price),
                    'review_score' => $review['score_total'],
                    'total_review' => $review['total_review'],
                    'is_wishlist' => $business->isWishList(),
                ];
            })
            ->sortByDesc('review_score')
            ->take(6)
            ->values();

        $plans = Plan::where('status', 'publish')
            ->orderBy('price', 'asc')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'title' => $plan->title,
                    'content' => $plan->content,
                    'price' => $plan->price,
                    'price_html' => format_money($plan->price),
                    'annual_price' => $plan->annual_price,
                    'annual_html' => $plan->annual_price ? format_money($plan->annual_price) : null,
                    'duration' => $plan->duration,
                    'duration_type' => $plan->duration_type,
                    'duration_text' => $plan->duration_text,
                    'max_service' => $plan->max_service,
                    'max_ipanorama' => $plan->max_ipanorama,
                    'is_recommended' => $plan->is_recommended,
                ];
            });

        $totalUsers = User::count();

        $data = [
            'categories' => $categories,
            'accommodations' => $accommodations,
            'properties' => $properties,
            'businesses' => $businesses,
            'plans' => $plans,
            'total_users' => $totalUsers,
            'page_title' => __('Virtuard - Immersive Digital Spaces'),
        ];

        // dd($data);
        return view('v2.home.index', $data);
    }

    /**
     * Get summary of space categories with count and minimum starting price.
     */
    private function getSpaceCategories(): array
    {
        $accommodationCount = Hotel::where('status', 'publish')->count();
        $accommodationMinPrice = Hotel::where('status', 'publish')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->min('price');

        $propertyCount = Space::where('status', 'publish')->count();
        $propertyMinPrice = Space::where('status', 'publish')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->min('price');

        $businessCount = Business::where('status', 'publish')->count();
        $businessMinPrice = Business::where('status', 'publish')
            ->whereNotNull('price')
            ->where('price', '>', 0)
            ->min('price');

        return [
            [
                'name' => __('Accommodation'),
                'count' => $accommodationCount,
                'min_price' => $accommodationMinPrice,
                'price_html' => $accommodationMinPrice ? format_money($accommodationMinPrice) : null,
                'url' => url(config('hotel.hotel_route_prefix', 'hotel')),
            ],
            [
                'name' => __('Property'),
                'count' => $propertyCount,
                'min_price' => $propertyMinPrice,
                'price_html' => $propertyMinPrice ? format_money($propertyMinPrice) : null,
                'url' => url(config('space.space_route_prefix', 'space')),
            ],
            [
                'name' => __('Commercial Activities'),
                'count' => $businessCount,
                'min_price' => $businessMinPrice,
                'price_html' => $businessMinPrice ? format_money($businessMinPrice) : null,
                'url' => url(config('business.business_route_prefix', 'business')),
            ],
        ];
    }
}
