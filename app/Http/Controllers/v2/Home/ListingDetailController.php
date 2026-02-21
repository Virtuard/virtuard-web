<?php

namespace App\Http\Controllers\v2\Home;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Booking\Models\Enquiry;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Business\Models\Business;
use Modules\Review\Models\Review;

class ListingDetailController extends Controller
{
    /**
     * Hotel detail page.
     */
    public function hotel(Request $request, $slug)
    {
        $row = Hotel::where('slug', $slug)
            ->with(['location', 'translation', 'hasWishList'])
            ->first();

        if (empty($row) || !$row->hasPermissionDetailView()) {
            abort(404);
        }

        $this->trackView('hotel', $row);

        if (!empty($request['preview_panorama'])) {
            return view_panorama('hotel', $row);
        }

        return view('v2.listing.hotel', $this->buildDetailData($row, 'hotel'));
    }

    /**
     * Space/Property detail page.
     */
    public function space(Request $request, $slug)
    {
        $row = Space::where('slug', $slug)
            ->with(['location', 'translation', 'hasWishList'])
            ->first();

        if (empty($row) || !$row->hasPermissionDetailView()) {
            abort(404);
        }

        $this->trackView('space', $row);

        if (!empty($request['preview_panorama'])) {
            return view_panorama('space', $row);
        }

        return view('v2.listing.space', $this->buildDetailData($row, 'space'));
    }

    /**
     * Business detail page.
     */
    public function business(Request $request, $slug)
    {
        $row = Business::where('slug', $slug)
            ->with(['location', 'translation', 'hasWishList'])
            ->first();

        if (empty($row) || !$row->hasPermissionDetailView()) {
            abort(404);
        }

        $this->trackView('business', $row);

        if (!empty($request['preview_panorama'])) {
            return view_panorama('business', $row);
        }

        return view('v2.listing.business', $this->buildDetailData($row, 'business'));
    }

    /**
     * Send enquiry to the host (AJAX).
     */
    public function sendEnquiry(Request $request)
    {
        $request->validate([
            'object_id' => 'required|integer',
            'object_model' => 'required|in:hotel,space,business',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'note' => 'nullable|string|max:500',
        ]);

        // Find the listing to get vendor_id
        $vendorId = null;
        $model = match ($request->object_model) {
            'hotel' => Hotel::find($request->object_id),
            'space' => Space::find($request->object_id),
            'business' => Business::find($request->object_id),
        };

        if ($model) {
            $vendorId = $model->create_user;
        }

        $enquiry = Enquiry::create([
            'object_id' => $request->object_id,
            'object_model' => $request->object_model,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'note' => $request->note,
            'status' => 'pending',
            'vendor_id' => $vendorId,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('Enquiry sent successfully! The host will contact you soon.'),
        ]);
    }

    private function buildDetailData($row, string $type): array
    {
        $translation = $row->translate();
        $reviewList = $row->getReviewList();
        $reviewScore = $row->getScoreReview();

        // Related listings (same location)
        $relatedItems = [];
        if (!empty($row->location_id)) {
            $modelClass = get_class($row);
            $relatedItems = $modelClass::where('location_id', $row->location_id)
                ->where('status', 'publish')
                ->whereNotIn('id', [$row->id])
                ->take(4)
                ->with(['location', 'translation', 'hasWishList'])
                ->get()
                ->map(fn($item) => $this->formatRelatedItem($item));
        }

        // Host/author info
        $author = $row->author ?? null;
        $host = null;
        if ($author) {
            $host = [
                'id' => $author->id,
                'name' => $author->getDisplayName(),
                'photo_profile' => $author->getAvatarUrl(),
                'member_since' => $author->created_at,
            ];
        }

        // Gallery images
        $gallery = [];
        if (!empty($row->gallery)) {
            $galleryIds = is_string($row->gallery) ? explode(',', $row->gallery) : $row->gallery;
            foreach ($galleryIds as $imgId) {
                $url = get_file_url($imgId, 'full');
                if ($url)
                    $gallery[] = $url;
            }
        }

        // Listing attributes/amenities (from terms)
        $terms = [];
        if (method_exists($row, 'terms')) {
            $termsRelation = $row->terms;
            if ($termsRelation) {
                $terms = $termsRelation->groupBy('attr_id')->map(function ($group) {
                    return $group->map(fn($t) => [
                        'id' => $t->id,
                        'name' => $t->name,
                        'icon' => $t->icon ?? null,
                    ]);
                });
            }
        }

        return [
            'page_title' => $translation->title ?? $row->title,
            'type' => $type,
            'row' => $row,
            'listing' => [
                'id' => $row->id,
                'title' => $translation->title ?? $row->title,
                'slug' => $row->slug,
                'content' => $translation->content ?? $row->content,
                'image' => $row->getImageUrl(),
                'gallery' => $gallery,
                'address' => $row->address,
                'map_lat' => $row->map_lat,
                'map_lng' => $row->map_lng,
                'map_zoom' => $row->map_zoom ?? 12,
                'location_name' => $row->location ? $row->location->name : null,
                'price' => $row->price,
                'sale_price' => $row->sale_price,
                'price_html' => format_money($row->sale_price ?: $row->price),
                'bed' => $row->bed ?? null,
                'bathroom' => $row->bathroom ?? null,
                'square' => $row->square ?? null,
                'max_guests' => $row->max_guests ?? null,
                'is_wishlist' => $row->isWishList(),
                'has_360' => !empty($row->ipanorama_url),
                'ipanorama_url' => $row->ipanorama_url ?? null,
                'share_url' => $row->getDetailUrl(false),
                'created_at' => $row->created_at,
            ],
            'host' => $host,
            'review' => [
                'score_total' => $reviewScore['score_total'],
                'total_review' => $reviewScore['total_review'],
                'rate_score' => $reviewScore['rate_score'] ?? [],
                'list' => $reviewList,
            ],
            'terms' => $terms,
            'booking_data' => $row->getBookingData(),
            'related' => $relatedItems,
            'seo_meta' => $row->getSeoMetaWithTranslation(app()->getLocale(), $translation),
        ];
    }

    private function formatRelatedItem($item): array
    {
        $review = $item->getScoreReview();

        return [
            'id' => $item->id,
            'title' => $item->title,
            'slug' => $item->slug,
            'url' => $item->getDetailUrl(false),
            'image' => $item->getImageUrl(),
            'location_name' => $item->location ? $item->location->name : null,
            'price' => $item->price,
            'price_html' => format_money($item->sale_price ?: $item->price),
            'review_score' => $review['score_total'],
            'total_review' => $review['total_review'],
            'is_wishlist' => $item->isWishList(),
        ];
    }

    private function trackView(string $type, $row): void
    {
        $ipKey = $type . '_viewed_' . $row->id;
        if (!session()->has($ipKey)) {
            $row->incrementViewCount();
            session()->put($ipKey, true);
        }
    }
}
