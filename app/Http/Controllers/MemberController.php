<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FollowUser;
use App\Notifications\PrivateChannelServices;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class MemberController extends Controller
{
    protected $user;
    protected $followUser;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->user = new User();
        $this->followUser = new FollowUser();
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $data = [
            'pageTitle' => 'Members',
            'memberCount' => 0,
            'followerCount' => 0,
            'followingCount' => 0,
            'seo_meta' => seo_attributes(),
        ];

        if (isset($request->type)) {
            if ($request->type == 'follower') {
                $data['pageTitle'] = 'Followers';
            }
            else if ($request->type == 'following') {
                $data['pageTitle'] = 'Following';
            }
        }

        $data['memberCount'] = $this->user
        ->where([
            ['role_id', '!=', 1],
            ['status', '=', 'publish'],
        ])
        ->count();

        if (auth()->check()) {
            $data['followerCount'] = auth()->user()->followers->count();
            $data['followingCount'] = auth()->user()->followings->count();
        }

        $data['users'] = $this->user
        ->withCount([
            'followers as followersCount' => function ($query) {
                $query->select(DB::raw("count(*)"));
            },
            'followings as followingCount' => function ($query) {
                $query->select(DB::raw("count(*)"));
            }
        ])
        ->when(auth()->check(), function ($q) {
            $q->where('id', '!=', auth()->user()->id);
        })
        ->when(isset($request->search), function ($q) use ($request) {
            return $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
        })
        ->when(isset($request->type), function ($q) use ($request) {
            if ($request->type == 'following') {
                return $q->whereHas('followers', function ($q1) {
                    $q1->where('user_id', auth()->user()->id);
                });
            } elseif ($request->type == 'follower') {
                return $q->whereHas('followings', function ($q1) {
                    $q1->where('follower_id', auth()->user()->id);
                });
            }
        })
        ->where([
            ['role_id', '!=', 1],
            ['status', '=', 'publish'],
        ])
        ->orderBy('last_login_at', 'DESC')
        ->orderBy('id', 'DESC')
        ->paginate(15);
    
        return view('app.members.index', $data);
    }

    public function store(Request $request) {
        $data = $request->all();
        $data['user_id'] = auth()->user()->id;
        
        if ($data['action'] === 'follow') {
            FollowUser::create($data);
    
            $messageData = [
                'id' => uniqid(), 
                'message' => 'started following you', 
                'attachment' => null 
            ];
            $this->notifyUser($request, $messageData);
    
            return back()->with([
                'status' => 'success',
                'message' => 'Following success'
            ]);
        } else {
            FollowUser::where([
                ['user_id', $data['user_id']],
                ['follower_id', $data['follower_id']],
            ])->delete();
    
            $messageData = [
                'id' => uniqid(),
                'message' => 'unfollowed you',
                'attachment' => null
            ];
            $this->notifyUser($request, $messageData);
    
            return back()->with([
                'status' => 'success',
                'message' => 'Unfollowing success'
            ]);
        }
    }
    

    protected function notifyUser(Request $request, $message) {
        $currentUser = auth()->user();
        $toUser = User::find($request->follower_id); 
    
        if (!$toUser) return;
    
       
        $message_content = __(':name :message', [
            'name' => $currentUser->display_name, 
            'message' => $message['message']
        ]);
    
        $data = [
            'id' =>  $message['id'],
            'event' => 'FollowUser',
            'to' => 'user',
            'name' => $currentUser->display_name,
            'avatar' => '',
            'link' => route('user.profile', ['id' => $currentUser->id]), 
            'type' => 'follow',
            'message' => $message_content
        ];
    
        $toUser->notify(new PrivateChannelServices($data));
    }
    
    
}
