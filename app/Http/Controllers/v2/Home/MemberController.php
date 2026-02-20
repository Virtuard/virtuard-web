<?php

namespace App\Http\Controllers\v2\Home;

use App\Http\Controllers\Controller;
use App\Models\FollowUser;
use App\Models\Ipanorama;
use App\Models\UserPost;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Hotel\Models\Hotel;
use Modules\Space\Models\Space;
use Modules\Business\Models\Business;

class MemberController extends Controller
{
    protected $perPage = 12;

    /**
     * All members page with search, filter, and sorting.
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $keyword = $request->input('keyword');

        $query = User::query()
            ->where('status', 'publish')
            ->whereNull('deleted_at');

        if (Auth::check()) {
            $userId = Auth::id();
            if ($filter === 'followers') {
                $followerIds = FollowUser::where('follower_id', $userId)->pluck('user_id');
                $query->whereIn('id', $followerIds);
            } elseif ($filter === 'following') {
                $followingIds = FollowUser::where('user_id', $userId)->pluck('follower_id');
                $query->whereIn('id', $followingIds);
            }
        }

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('user_name', 'like', "%{$keyword}%")
                    ->orWhere('first_name', 'like', "%{$keyword}%")
                    ->orWhere('last_name', 'like', "%{$keyword}%")
                    ->orWhere('business_name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        $query->orderByDesc('last_login_at')
            ->orderByDesc('created_at');

        $query->withCount(['followers', 'followings']);

        $members = $query->paginate($this->perPage)->withQueryString();

        $members->getCollection()->transform(function ($user) {
            return $this->formatMember($user);
        });

        $data = [
            'page_title' => __('All Members'),
            'members' => $members,
            'currentFilter' => $filter,
            'keyword' => $keyword,
        ];

        return view('v2.member.index', $data);
    }

    /**
     * Member detail page with 5 tabs.
     */
    public function show(Request $request, $userName)
    {
        $user = User::where('user_name', $userName)
            ->where('status', 'publish')
            ->firstOrFail();

        $tab = $request->input('tab', 'profile');

        // Member profile data
        $profile = [
            'id' => $user->id,
            'name' => $user->getDisplayName(),
            'user_name' => $user->user_name,
            'bio' => $user->bio,
            'business_name' => $user->business_name,
            'photo_profile' => $user->getAvatarUrl('full'),
            'email' => $user->email,
            'posts_count' => UserPost::where('user_id', $user->id)->count(),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->followings()->count(),
            'is_following' => Auth::check()
                ? FollowUser::where('user_id', Auth::id())->where('follower_id', $user->id)->exists()
                : false,
            'social_links' => [
                'website' => $user->website_url,
                'instagram' => $user->instagram_url,
                'facebook' => $user->facebook_url,
                'twitter' => $user->twitter_url,
                'linkedin' => $user->linkedin_url,
            ],
            'created_at' => $user->created_at,
        ];

        // Tab data
        $tabData = match ($tab) {
            'virtual_tour' => $this->getVirtualTours($user),
            'accommodation' => $this->getAccommodations($user),
            'property' => $this->getProperties($user),
            'commercial_activities' => $this->getCommercialActivities($user),
            default => $this->getProfilePosts($user, $request),
        };

        $data = [
            'page_title' => $user->getDisplayName(),
            'profile' => $profile,
            'currentTab' => $tab,
            'tabData' => $tabData,
        ];

        return view('v2.member.show', $data);
    }

    private function getProfilePosts($user, Request $request)
    {
        $query = UserPost::where('user_id', $user->id)
            ->withCount(['likes', 'comments'])
            ->with(['medias', 'author.mediaFile'])
            ->orderByDesc('created_at');

        if (Auth::check()) {
            $userId = Auth::id();
            $query->with(['likes' => fn($q) => $q->where('user_id', $userId)]);
        }

        $posts = $query->paginate(10)->withQueryString();

        $posts->getCollection()->transform(function ($post) {
            $author = $post->author;
            return [
                'id' => $post->id,
                'message' => $post->message,
                'type_post' => $post->type_post,
                'created_at' => $post->created_at,
                'likes_count' => $post->likes_count,
                'comments_count' => $post->comments_count,
                'is_liked' => Auth::check() ? $post->likes->count() > 0 : false,
                'medias' => $post->medias->map(fn($m) => [
                    'id' => $m->id,
                    'url' => url('/storage/' . $m->media),
                    'type' => $m->type,
                    'is_360_media' => $m->is_360_media ?? false,
                ]),
                'author' => [
                    'id' => $author->id ?? null,
                    'name' => $author ? $author->name : 'Unknown',
                    'user_name' => $author->user_name ?? null,
                    'photo_profile' => ($author && $author->mediaFile)
                        ? url('/uploads/' . $author->mediaFile->file_path)
                        : url('/uploads/images/virtuard.png'),
                ],
            ];
        });

        return $posts;
    }

    private function getVirtualTours($user)
    {
        return Ipanorama::where('user_id', $user->id)
            ->where('status', 'publish')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($item) => [
                'id' => $item->id,
                'title' => $item->title,
                'thumb' => $item->thumb ? url('/uploads/' . $item->thumb) : null,
                'code' => $item->code,
                'uuid' => $item->uuid ?? null,
                'created_at' => $item->created_at,
            ]);
    }

    private function getAccommodations($user)
    {
        return Hotel::where('create_user', $user->id)
            ->where('status', 'publish')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($item) => $this->formatListing($item));
    }

    private function getProperties($user)
    {
        return Space::where('create_user', $user->id)
            ->where('status', 'publish')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($item) => $this->formatListing($item));
    }

    private function getCommercialActivities($user)
    {
        return Business::where('create_user', $user->id)
            ->where('status', 'publish')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn($item) => $this->formatListing($item));
    }

    private function formatMember($user): array
    {
        $postsCount = UserPost::where('user_id', $user->id)->count();

        return [
            'id' => $user->id,
            'name' => $user->getDisplayName(),
            'user_name' => $user->user_name,
            'bio' => $user->bio,
            'business_name' => $user->business_name,
            'photo_profile' => $user->getAvatarUrl(),
            'posts_count' => $postsCount,
            'followers_count' => $user->followers_count,
            'following_count' => $user->followings_count,
            'last_login_at' => $user->last_login_at,
            'created_at' => $user->created_at,
        ];
    }

    private function formatListing($item): array
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
            'sale_price' => $item->sale_price,
            'price_html' => format_money($item->sale_price ?: $item->price),
            'bed' => $item->bed ?? null,
            'bathroom' => $item->bathroom ?? null,
            'review_score' => $review['score_total'],
            'total_review' => $review['total_review'],
            'is_wishlist' => $item->isWishList(),
            'created_at' => $item->created_at,
        ];
    }
}
