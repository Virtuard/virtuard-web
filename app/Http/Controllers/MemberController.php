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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $idUser = Auth::id();
        $dataUser = User::leftJoin('follow_member', 'users.id', '=', 'follow_member.user_id')
            ->select('users.*', DB::raw('IFNULL(follow_member.follow_user_id, false) as isFollow'))
            ->get();

        $dataPostMe = UserPost::join('users', 'users.id', '=', 'user_post_status.user_id')
        ->select('user_post_status.*', 'users.name as name')
        ->where('users.id', $idUser)
        ->orderBy('user_post_status.created_at', 'desc') 
        ->get();

        return view('members.index', compact('dataUser', 'dataPostMe'));
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
