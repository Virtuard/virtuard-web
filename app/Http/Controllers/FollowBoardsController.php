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
use App\Models\RefIpanorama;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FollowBoardsController extends Controller
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
        ->when(isset($request->filter), function($q) use ($request){
            if (auth()->check()) {
                if ($request->filter == 'me') {
                    $q->where('user_id', auth()->user()->id);
                }
                elseif ($request->filter == 'friend') {
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
        
        $memberCount = User::count();
        $idUser = Auth::id();
        $dataIpanorama = RefIpanorama::where([
            ['id_user', $idUser],
            ['status', 'publish'],
        ])->get();
        $myFeeds = Story::where('user_id', $idUser)
            ->orderByDesc('id')
            ->paginate(50)
            ->withQueryString();

        $data = [
            'posts' => $posts,
            'memberCount' => $memberCount,
            'dataIpanorama' => $dataIpanorama,
            'myFeeds' => $myFeeds,
        ];

        if (auth()->check()) {
            $data['followerCount'] = auth()->user()->followers->count();
            $data['followingCount'] = auth()->user()->followings->count();
        }

        return view('boards.index', $data);
    }

    public function store(Request $request)
    {
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
        return back()->with('success', 'Updated status');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something wrong!');
        }
    }

    public function likePost($id)
    {
        $idUser = Auth::id();

        $postLike = PostLike::where('post_id', $id)
            ->where('user_id', $idUser)
            ->first();

        if (!$postLike) {
            $like = new PostLike();
            $like->post_id = $id;
            $like->user_id = $idUser;
            $like->save();

            return redirect()->to(url()->previous() . '#Post-' . $id);
        } else {
            $postLike->delete();
            return redirect()->to(url()->previous() . '#Post-' . $id);
        }
    }

    public function commentPost(Request $request, $id)
    {
        $idUser = Auth::id();

        $request->validate([
            'comment' => 'required',
        ]);

        $comment = new PostComment();
        $comment->post_id = $id;
        $comment->user_id = $idUser;
        $comment->comment = $request->input('comment');
        $comment->save();

        return redirect()->to(url()->previous() . '#Post-' . $id);
    }
}
