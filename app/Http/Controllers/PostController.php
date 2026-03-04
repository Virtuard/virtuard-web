<?php

namespace App\Http\Controllers;

use App\Models\FollowUser;
use App\User;
use Illuminate\Support\Facades\Log;
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
        $idUser = auth()->check() ? auth()->user()->id : null;
        
        $posts = $this->userPost
            ->with(['ipanorama', 'medias', 'likes', 'comments'])
            ->whereHas('user')
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
            ->where(function ($q) use ($idUser, $request) {
                if ($request->has('type') && $request->type) {
                    $typeFilter = $request->type;
                    if ($typeFilter == 'public') {
                        $q->where(function ($q2) {
                            $q2->where('type_post', 'public')
                               ->orWhereNull('type_post')
                               ->orWhere('type_post', '');
                        });
                        if ($idUser) {
                            $q->orWhere('user_id', $idUser);
                        }
                    } elseif ($typeFilter == 'friend') {
                        $q->where(function ($query) use ($idUser) {
                            if ($idUser) {
                                $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                                $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                                $friend_ids = array_merge($following_ids, $follower_ids);
                                if (!empty($friend_ids)) {
                                    $query->where('type_post', 'friend')
                                          ->whereIn('user_id', $friend_ids);
                                } else {
                                    $query->where('user_id', 0);
                                }
                            } else {
                                $query->where('user_id', 0);
                            }
                        });
                    } elseif ($typeFilter == 'private') {
                        if ($idUser) {
                            $q->where('type_post', 'private')
                              ->where('user_id', $idUser);
                        } else {
                            $q->where('user_id', 0);
                        }
                    }
                } else {
                    $q->where(function ($query) use ($idUser) {
                        if ($idUser) {
                            $query->where('user_id', $idUser);
                        }
                        
                        $query->orWhere(function ($q2) {
                            $q2->where('type_post', 'public')
                               ->orWhereNull('type_post')
                               ->orWhere('type_post', '');
                        });
                        
                        if ($idUser) {
                            $query->orWhere(function ($q3) use ($idUser) {
                                $q3->where('type_post', 'friend')
                                   ->where(function ($q4) use ($idUser) {
                                       $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                                       $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                                       $friend_ids = array_merge($following_ids, $follower_ids);
                                       if (!empty($friend_ids)) {
                                           $q4->whereIn('user_id', $friend_ids);
                                       } else {
                                           $q4->where('user_id', 0);
                                       }
                                   });
                            });
                        }
                    });
                }
            })
            ->orderBy('id', 'desc')
            ->paginate(10)
            ->withQueryString();

        $panorama_posts = $this->userPost
            ->with(['ipanorama', 'medias', 'likes', 'comments'])
            ->whereHas('user')
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
            ->where(function ($q) use ($idUser, $request) {
                if ($request->has('type') && $request->type) {
                    $typeFilter = $request->type;
                    if ($typeFilter == 'public') {
                        $q->where(function ($q2) {
                            $q2->where('type_post', 'public')
                               ->orWhereNull('type_post')
                               ->orWhere('type_post', '');
                        });
                        if ($idUser) {
                            $q->orWhere('user_id', $idUser);
                        }
                    } elseif ($typeFilter == 'friend') {
                        $q->where(function ($query) use ($idUser) {
                            if ($idUser) {
                                $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                                $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                                $friend_ids = array_merge($following_ids, $follower_ids);
                                if (!empty($friend_ids)) {
                                    $query->where('type_post', 'friend')
                                          ->whereIn('user_id', $friend_ids);
                                } else {
                                    $query->where('user_id', 0);
                                }
                            } else {
                                $query->where('user_id', 0);
                            }
                        });
                    } elseif ($typeFilter == 'private') {
                        if ($idUser) {
                            $q->where('type_post', 'private')
                              ->where('user_id', $idUser);
                        } else {
                            $q->where('user_id', 0);
                        }
                    }
                } else {
                    $q->where(function ($query) use ($idUser) {
                        if ($idUser) {
                            $query->where('user_id', $idUser);
                        }
                        
                        $query->orWhere(function ($q2) {
                            $q2->where('type_post', 'public')
                               ->orWhereNull('type_post')
                               ->orWhere('type_post', '');
                        });
                        
                        if ($idUser) {
                            $query->orWhere(function ($q3) use ($idUser) {
                                $q3->where('type_post', 'friend')
                                   ->where(function ($q4) use ($idUser) {
                                       $following_ids = FollowUser::where('user_id', $idUser)->pluck('follower_id')->toArray();
                                       $follower_ids = FollowUser::where('follower_id', $idUser)->pluck('user_id')->toArray();
                                       $friend_ids = array_merge($following_ids, $follower_ids);
                                       if (!empty($friend_ids)) {
                                           $q4->whereIn('user_id', $friend_ids);
                                       } else {
                                           $q4->where('user_id', 0);
                                       }
                                   });
                            });
                        }
                    });
                }
            })
            ->where('ipanorama_id', '!=', null)
            ->orderBy('id', 'desc')
            ->paginate(10)
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
            ->paginate(20)
            ->withQueryString();

        $data = [
            'posts' => $posts,
            'panorama_posts' => $panorama_posts,
            'memberCount' => $memberCount,
            'dataIpanorama' => $dataIpanorama,
            'feeds' => $feeds,
            'seo_meta' => seo_attributes(),
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
            'media_user.*' => 'nullable|mimes:jpeg,png,mp4,mov,mkv,insv|max:100000',
        ], [
            'media_user.*.mimes' => 'File extention denied',
            'media_user.*.max' => 'Maximum upload file 100MB'
        ]);

        if (isset($request->media_user) || $request->ipanorama_id) {
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
                'type_post' =>  $request->input('type_post') ?: 'public', // Default to 'public' if empty
                'tag' =>  '-',
            ];
            $post = UserPost::create($dataPost);

            if ($request->hasFile('media_user')) {
                $files = $request->file('media_user');
                foreach ($files as $file) {
                    $filename = time() . '-' . $file->getClientOriginalName();
                    $extension = strtolower($file->getClientOriginalExtension());
                    $type = getMimeTypeFromExtension($extension);
                    $path = $file->storeAs('/media', $filename);

                    $dataMedia = [
                        'post_id' => $post->id,
                        'media' => $path,
                        'type' => $type,
                        'is_360_media' => $request->is_360_media === 'on',
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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => (bool)!$postLike,
                'like_count' => $post->likes->count()
            ]);
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

    // public function destroyComment($id)
    // {
    //     PostComment::destroy($id);

    //     return back()->with('success', 'Deleted status');
    // }

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

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'comment_id' => $comment->id,
                'comment' => $comment->comment,
                'user' => [
                    'name' => auth()->user()->display_name ?? auth()->user()->name,
                    'avatar' => auth()->user()->getAvatarUrl() ?? asset('images/avatar.png')
                ],
                'created_at' => $comment->created_at->diffForHumans()
            ]);
        }

        return redirect()->to(url()->previous() . '#Post-' . $id);
    }

    public function updateComment(Request $request, $id)
    {
        $request->validate([
            'comment' => 'required',
        ]);

        $comment = PostComment::findOrFail($id);


        if ($comment->user_id !== auth()->id()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            return back()->with('error', 'Unauthorized');
        }

        $comment->comment = $request->input('comment');
        $comment->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'comment_id' => $comment->id,
                'comment' => $comment->comment,
                'updated_at' => $comment->updated_at->diffForHumans()
            ]);
        }

        return back()->with('success', 'Comment updated');
    }

    public function destroyComment($id)
    {
        $comment = PostComment::findOrFail($id);


        if ($comment->user_id !== auth()->id()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
            return back()->with('error', 'Unauthorized');
        }

        $postId = $comment->post_id;
        $commentId = $comment->id;
        $comment->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'comment_id' => $commentId,
                'post_id' => $postId
            ]);
        }

        return back()->with('success', 'Deleted comment');
    }
}
