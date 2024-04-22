<?php
namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FollowUser;

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
        ];

        if (isset($request->type)) {
            if ($request->type == 'follower') {
                $data['pageTitle'] = 'Followers';
            }
            else if ($request->type == 'following') {
                $data['pageTitle'] = 'Following';
            }
        }

        $data['memberCount'] = $this->user->where([
            ['role_id', '!=', 1]
        ])->count();

        if (auth()->check()) {
            $data['followerCount'] = auth()->user()->followers->count();
            $data['followingCount'] = auth()->user()->followings->count();
        }

        $data['users'] = $this->user
            ->when(auth()->check(), function($q){
                $q->where('id', '!=', auth()->user()->id);
            })
            ->when(isset($request->search), function($q) use ($request){
                return $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->when(isset($request->type), function($q) use ($request) {
                if ($request->type == 'following') {
                    return $q->whereHas('followers', function ($q1) {
                        $q1->where('user_id', auth()->user()->id);
                    });
                }
                elseif ($request->type == 'follower') {
                    return $q->whereHas('followings', function ($q1) {
                        $q1->where('follow_user_id', auth()->user()->id);
                    });
                }
            })
            ->where([
                ['role_id', '!=', 1]
            ])
            ->orderBy('id', 'DESC')
            ->get();

        return view('members.index', $data);
    }

    public function store(Request $request) {
        $idUser = Auth::id();
        $idFollowUser = $request->input('id_follow');
        $indicator = $request->input('param');

        if($indicator === 'Follow'){
            $data = [
                'user_id' => $idUser,
                'follow_user_id' => $idFollowUser,
            ];
            FollowUser::create($data);

            return back()->with([
                'status' => 'success',
                'message' => 'Following success'
            ]);
        }else{
            FollowUser::where([
                ['user_id', $idUser],
                ['follow_user_id', $idFollowUser],
            ])->delete();

            return back()->with([
                'status' => 'success',
                'message' => 'Unfollowing success'
            ]);
        }

    }
}
