<?php

namespace App\Http\Controllers;

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
    public function index()
    {
        $posts = $this->userPost
        ->with(['ipanorama', 'medias', 'likes', 'comments'])
        ->orderBy('id', 'desc')
        ->get();
        $userCount = User::count();
        $idUser = Auth::id();
        $dataIpanorama = RefIpanorama::where('id_user', $idUser)->get();
        $dataFeedMe = Story::where('user_id', $idUser)->get();

        return view('boards.index', compact('userCount', 'dataFeedMe', 'posts', 'dataIpanorama'));
    }

    public function store(Request $request)
    {
        $idUser = Auth::id();

        $post = new UserPost();
        $post->user_id = $idUser;
        $post->ipanorama_id = $request->input('ipanorama_id');
        $post->message = $request->input('message');
        $post->type_status = 'Status';
        $post->type_post = $request->input('type_post');
        $post->tag = '-';
        $post->save();

        if ($request->hasFile('media_user')) {
            $files = $request->file('media_user');

            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $path = $file->storeAs('/media', $filename);

                $mediaItem = new PostMedia();
                $mediaItem->post_id = $post->id;
                $mediaItem->media = $path;
                $mediaItem->save();
            }
        }

        return back()->with('success', 'Updated status');
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
