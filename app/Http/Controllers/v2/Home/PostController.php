<?php

namespace App\Http\Controllers\v2\Home;

use App\Http\Controllers\Controller;
use App\Models\UserPost;
use App\Models\PostMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    protected $perPage = 10;

    /**
     * All posts feed page.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'for_you'); // 'newest' or 'for_you'

        $query = UserPost::query()
            ->withCount(['likes', 'comments'])
            ->with(['medias', 'author.mediaFile']);

        // "For You" tab — all public posts
        // "Newest" tab — latest posts
        if ($tab === 'newest') {
            $query->orderBy('created_at', 'desc');
        } else {
            // For You: could be algorithm-based, for now just order by engagement
            $query->orderByDesc('created_at');
        }

        // Add is_liked for current user
        if (Auth::check()) {
            $userId = Auth::id();
            $query->with([
                'likes' => function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                }
            ]);
        }

        $posts = $query->paginate($this->perPage)->withQueryString();

        // Transform posts
        $posts->getCollection()->transform(function ($post) {
            return $this->formatPost($post);
        });

        $data = [
            'page_title' => __('All Posts'),
            'posts' => $posts,
            'currentTab' => $tab,
        ];

        return view('v2.post.index', $data);
    }

    /**
     * Store a new post (from the "Post Something" sidebar).
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required_without:media_user|nullable|string',
            'media_user' => 'nullable|array',
            'media_user.*' => 'file|mimes:jpg,jpeg,png,gif,mp4,webp|max:10240',
            'privacy' => 'nullable|in:public,friends,private',
            'is_360_media' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            $post = UserPost::create([
                'user_id' => Auth::id(),
                'message' => $request->input('message'),
                'type_status' => 'Status',
                'type_post' => $request->input('type_post', 'normal'),
                'tag' => '-',
            ]);

            if ($request->hasFile('media_user')) {
                foreach ($request->file('media_user') as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $type = getMimeTypeFromExtension($extension);
                    $path = $file->storeAs('/media', $filename);

                    PostMedia::create([
                        'post_id' => $post->id,
                        'media' => $path,
                        'type' => $type,
                        'is_360_media' => $request->input('is_360_media', false),
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', __('Post created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Post creation failed: " . $e->getMessage());
            return back()->with('error', __('Failed to create post'));
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function formatPost($post): array
    {
        $author = $post->author;

        return [
            'id' => $post->id,
            'message' => $post->message,
            'type_post' => $post->type_post,
            'created_at' => $post->created_at,
            'likes_count' => $post->likes_count,
            'comments_count' => $post->comments_count,
            'is_liked' => Auth::check() ? $post->likes->count() > 0 : false,
            'medias' => $post->medias->map(function ($media) {
                return [
                    'id' => $media->id,
                    'url' => url('/storage/' . $media->media),
                    'type' => $media->type,
                    'is_360_media' => $media->is_360_media ?? false,
                ];
            }),
            'author' => [
                'id' => $author->id ?? null,
                'name' => $author ? $author->name : 'Unknown',
                'user_name' => $author->user_name ?? null,
                'photo_profile' => ($author && $author->mediaFile)
                    ? url('/uploads/' . $author->mediaFile->file_path)
                    : url('/uploads/images/virtuard.png'),
            ],
        ];
    }
}
