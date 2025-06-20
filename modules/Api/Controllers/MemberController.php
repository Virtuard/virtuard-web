<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FollowUser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MemberController extends Controller
{
    protected $user;
    protected $followUser;

    public function __construct()
    {
        $this->user = new User();
        $this->followUser = new FollowUser();
    }

    public function allMembers(Request $request)
    {
        $data = [
            'pageTitle' => 'Members',
            'memberCount' => 0,
            'followerCount' => 0,
            'followingCount' => 0,
        ];
        
        try{
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
                    return $q->where('name', 'like', '%' . $request->search . '%');
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

            $data['users']->getCollection()->transform(function ($user) {
                $user->avatar_url = $user->getAvatarUrl();
                return $user;
            });
            
            Log::info("Data member: " );
            Log::info($data['users']);;

            return response()->json([
                'status' => true,
                'message' => 'success',
                'data' => $data,
            ]);
        }catch(Exception $exception ) {
            Log::info("INfo error masseh");
            Log::error($exception);
        }
        
    }

}
