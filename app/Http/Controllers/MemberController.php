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
use App\Models\FollowUser;
use Carbon\Carbon;
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
    public function index()
    {
        $users = $this->user
            ->where([
                ['role_id', '!=', 1]
            ])
            ->orderBy('id', 'DESC')
            ->get();

        $myFollowerCount = 0;

        if (auth()->check()) {
            $myFollowerCount = $this->followUser->where('follow_user_id', auth()->user()->id)->count();;
        } 

        return view('members.index', compact('users', 'myFollowerCount'));
    }

    public function store(Request $request) {
        $idUser = Auth::id();
        $idFollowUser = $request->input('id_follow');
        $indicator = $request->input('param');

        if($indicator === 'Follow'){
            $follow = new FollowUser();
            $follow->user_id = $idUser;
            $follow->follow_user_id = $idFollowUser;
            $follow->save();

            return back()->with('success', 'Following success');
        }else{
            $unfollow = FollowUser::where('user_id',$idUser)->orWhere('follow_user_id',$idFollowUser)->delete();

            return back()->with('success', 'Unfollowing success');
        }

    }

}
