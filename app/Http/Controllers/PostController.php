<?php

namespace App\Http\Controllers;

use App\Models\FollowUser;
use App\User;
use Modules\Hotel\Models\Hotel;
use Modules\Location\Models\LocationCategory;
use Modules\Page\Models\Page;
use Modules\News\Models\NewsCategory;
use Modules\News\Models\Tag;
use Modules\News\Models\News;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserPost;
use App\Models\PostMedia;
use App\Models\PostLike;
use App\Models\PostComment;
use App\Models\Story;
use App\Models\Ipanorama;
use App\Notifications\PrivateChannelServices;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    protected $userPost;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userPost = new UserPost();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $posts = $this->userPost
            ->with(['ipanorama', 'medias', 'likes', 'comments'])
            ->when(isset($request->filter), function ($q) use ($request) {
                if (auth()->check()) {
                    if ($request->filter == 'me') {
                        $q->where('user_id', auth()->user()->id);
                    } elseif ($request->filter == 'friend') {
                        $following_ids = FollowUser::where('user_id', auth()->user()->id)->pluck('follower_id')->toArray();
                        $follwer_ids = FollowUser::where('follower_id', auth()->user()->id)->pluck('user_id')->toArray();
                        $ids = array_merge($following_ids, $follwer_ids);
                        $q->whereIn('user_id', $ids);
                    }
                }
            })
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $panorama_posts = $this->userPost    
            ->with(['ipanorama', 'medias', 'likes', 'comments'])
            ->when(isset($request->filter), function ($q) use ($request) {
                if (auth()->check()) {
                    if ($request->filter == 'me') {
                        $q->where('user_id', auth()->user()->id);
                    } elseif ($request->filter == 'friend') {
                        $following_ids = FollowUser::where('user_id', auth()->user()->id)->pluck('follower_id')->toArray();
                        $follwer_ids = FollowUser::where('follower_id', auth()->user()->id)->pluck('user_id')->toArray();
                        $ids = array_merge($following_ids, $follwer_ids);
                        $q->whereIn('user_id', $ids);
                    }
                }
            })
            ->where('ipanorama_id', '!=', null)
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $memberCount = User::count();
        $idUser = Auth::id();
        $dataIpanorama = Ipanorama::where([
            ['user_id', $idUser],
            ['status', 'publish'],
        ])->get();
        $feeds = Story::query()
            ->when(isset($request->filter), function ($q) use ($request) {
                if (auth()->check()) {
                    if ($request->filter == 'me') {
                        $q->where('user_id', auth()->user()->id);
                    } elseif ($request->filter == 'friend') {
                        $following_ids = FollowUser::where('user_id', auth()->user()->id)->pluck('follower_id')->toArray();
                        $follwer_ids = FollowUser::where('follower_id', auth()->user()->id)->pluck('user_id')->toArray();
                        $ids = array_merge($following_ids, $follwer_ids);
                        $q->whereIn('user_id', $ids);
                    }
                }
            })
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        $data = [
            'posts' => $posts,
            'panorama_posts' => $panorama_posts,
            'memberCount' => $memberCount,
            'dataIpanorama' => $dataIpanorama,
            'feeds' => $feeds,
        ];

        if (auth()->check()) {
            $data['followerCount'] = auth()->user()->followers->count();
            $data['followingCount'] = auth()->user()->followings->count();
        }

        return view('app.post.index', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'media_user.*' => 'nullable|mimes:jpeg,png,mp4|max:20000',
        ], [
            'media_user.*.mimes' => 'File extention denied',
            'media_user.*.max' => 'Maximum upload file 20MB'
        ]);

        if(isset($request->media_user) || $request->ipanorama_id){
            $this->validate($request, [
                'message' => 'nullable',
            ]);
        } else {
            $this->validate($request, [
                'message' => 'required',
            ]);
        }

        DB::beginTransaction();
        try {


            $dataPost = [
                'user_id' =>  auth()->user()->id,
                'ipanorama_id' =>  $request->input('ipanorama_id'),
                'message' =>  $request->input('message'),
                'type_status' =>  'Status',
                'type_post' =>  $request->input('type_post'),
                'tag' =>  '-',
            ];
            $post = UserPost::create($dataPost);

            if ($request->hasFile('media_user')) {
                $files = $request->file('media_user');
                foreach ($files as $file) {
                    $filename = $file->getClientOriginalName();
                    $extension = $file->getClientOriginalExtension();
                    $type = getMimeTypeFromExtension($extension);
                    $path = $file->storeAs('/media', $filename);

                    $dataMedia = [
                        'post_id' => $post->id,
                        'media' => $path,
                        'type' => $type,
                    ];
                    $mediaItem = PostMedia::create($dataMedia);
                }
            }

            DB::commit();
            return redirect()->route('post.index')->with('success', 'Updated status');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something wrong!');
        }
    }

    public function likePost(Request $request, $id)
    {
        $idUser = Auth::id();

        $post = UserPost::find($id);
        if (!$post) {
            return redirect()->back()->with('error', 'Post not found');
        }

        $postLike = PostLike::where('post_id', $id)
            ->where('user_id', $idUser)
            ->first();

        if (!$postLike) {
            $like = new PostLike();
            $like->post_id = $id;
            $like->user_id = $idUser;
            $like->save();

            $messageData = [
                'id' => $post->user_id,
                'message' => 'Liked your post',
            ];

            $this->notifyUser($post->user_id, $messageData, $id);
        } else {
            $postLike->delete();
        }
        return redirect()->to(url()->previous() . '#Post-' . $id);
    }

    protected function notifyUser($toUserId, $message, $postId)
    {
        $currentUser = auth()->user();
        $toUser = User::find($toUserId);

        if (!$toUser) return;

        $message_content = __(':name :message', [
            'name' => $currentUser->display_name,
            'message' => $message['message']
        ]);

        $data = [
            'id' => $toUserId,
            'notifiable_id' => $toUserId,
            'event' => 'LikeUser',
            'to' => 'user',
            'name' => $currentUser->display_name,
            'avatar' => $currentUser->profile_picture ?? '',
            'link' => url()->previous() . '#Post-' . $postId,
            'type' => 'like',
            'message' => $message_content
        ];

        $toUser->notify(new PrivateChannelServices($data));
    }


    public function storeComment(Request $request, $id)
    {
        $idUser = Auth::id();

        $request->validate([
            'comment' => 'required',
        ]);

        $post = UserPost::find($id);
        if (!$post) {
            return redirect()->back()->with('error', 'Post not found');
        }

        $comment = new PostComment();
        $comment->post_id = $id;
        $comment->user_id = $idUser;
        $comment->comment = $request->input('comment');
        $comment->save();

        $messageData = [
            'id' => $post->user_id,
            'message' => 'Commented on your post',
        ];

        $this->notifyUserComeent($post->user_id, $messageData, $id);

        return redirect()->to(url()->previous() . '#Post-' . $id);
    }

    protected function notifyUserComeent($toUserId, $message, $postId)
    {
        $currentUser = auth()->user();
        $toUser = User::find($toUserId);

        if (!$toUser) return;

        $message_content = __(':name :message', [
            'name' => $currentUser->display_name,
            'message' => $message['message']
        ]);

        $data = [
            'id' => $toUserId,
            'notifiable_id' => $toUserId,
            'event' => 'CommentUser',
            'to' => 'user',
            'name' => $currentUser->display_name,
            'avatar' => $currentUser->profile_picture ?? '',
            'link' => url()->previous() . '#Post-' . $postId,
            'type' => 'comment',
            'message' => $message_content
        ];

        $toUser->notify(new PrivateChannelServices($data));
    }


    public function destroy($id)
    {
        UserPost::destroy($id);

        return back()->with('success', 'Deleted status');
    }

    public function destroyComment($id)
    {
        PostComment::destroy($id);

        return back()->with('success', 'Deleted status');
    }
}
