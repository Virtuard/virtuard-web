<?php

/**
 * Created by PhpStorm.
 * User: h2 gaming
 * Date: 8/17/2019
 * Time: 3:05 PM
 */

namespace Modules\User\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\FrontendController;

class ProfileController extends FrontendController
{
    public function profile(Request $request, $id_or_slug)
    {
        $user = User::where('user_name', '=', $id_or_slug)->first();
        if (empty($user)) {
            $user = User::find($id_or_slug);
        }
        if (empty($user)) {
            abort(404);
        }

        if ($user->role_id == 1) {
            return redirect('/');
        }
        if (!$user->hasPermission('dashboard_vendor_access')) {
            return redirect('/');
        }

        $followerCount = DB::table('follow_member')
            ->where('follower_id', $user->id)
            ->count();

        $followingCount = DB::table('follow_member')
            ->where('user_id', $user->id)
            ->count();

        $followers = DB::table('follow_member')
            ->join('users', 'users.id', '=', 'follow_member.user_id')
            ->leftJoin('media_files', 'media_files.id', '=', 'users.avatar_id')
            ->where('follow_member.follower_id', $user->id)
            ->select('users.id', 'users.name', 'users.email', 'users.created_at','users.user_name', 'media_files.file_path')
            ->get();

        foreach ($followers as $follower) {
            $follower->avatar_url = $follower->file_path ? url('/uploads/' . $follower->file_path) : url('/images/avatar.png');
        }

        $following = DB::table('follow_member')
            ->join('users', 'users.id', '=', 'follow_member.follower_id')
            ->leftJoin('media_files', 'media_files.id', '=', 'users.avatar_id')
            ->where('follow_member.user_id', $user->id)
            ->select('users.id', 'users.name', 'users.email', 'users.created_at','users.user_name', 'media_files.file_path')
            ->get();

        foreach ($following as $follow) {
            $follow->avatar_url = $follow->file_path ? url('/uploads/' . $follow->file_path) : url('/images/avatar.png');
        }

        $profileUrl = url('/profile/' . $user->user_name);
        $referralUrl = url('/affiliate-' . $user->id . '_' . $user->user_name);
        $avatarUrl = $user->getAvatarUrl();

        $data['user'] = $user;
        $data['page_title'] = $user->getDisplayName();
        $data['followerCount'] = $followerCount;
        $data['followingCount'] = $followingCount;
        $data['profileUrl'] = $profileUrl;
        $data['referralUrl'] = $referralUrl;
        $data['avatarUrl'] = $avatarUrl;
        $data['followers'] = $followers;
        $data['following'] = $following;

        $this->registerCss('dist/frontend/module/user/css/profile.css');
        return view('User::frontend.profile.profile', $data);
    }



    public function alLReviews(Request $request, $id_or_slug)
    {
        $user = User::where('user_name', '=', $id_or_slug)->first();
        if (empty($user)) {
            $user = User::find($id_or_slug);
        }
        if (empty($user)) {
            abort(404);
        }
        $followerCount = DB::table('follow_member')
            ->where('follower_id', $user->id)
            ->count();

        $followingCount = DB::table('follow_member')
            ->where('user_id', $user->id)
            ->count();
        
            $followers = DB::table('follow_member')
            ->join('users', 'users.id', '=', 'follow_member.user_id')
            ->leftJoin('media_files', 'media_files.id', '=', 'users.avatar_id')
            ->where('follow_member.follower_id', $user->id)
            ->select('users.id', 'users.name', 'users.email', 'users.created_at','users.user_name', 'media_files.file_path')
            ->get();

        foreach ($followers as $follower) {
            $follower->avatar_url = $follower->file_path ? url('/uploads/' . $follower->file_path) : url('/images/avatar.png');
        }

        $following = DB::table('follow_member')
            ->join('users', 'users.id', '=', 'follow_member.follower_id')
            ->leftJoin('media_files', 'media_files.id', '=', 'users.avatar_id')
            ->where('follow_member.user_id', $user->id)
            ->select('users.id', 'users.name', 'users.email', 'users.created_at','users.user_name', 'media_files.file_path')
            ->get();

        foreach ($following as $follow) {
            $follow->avatar_url = $follow->file_path ? url('/uploads/' . $follow->file_path) : url('/images/avatar.png');
        }

        $profileUrl = url('/profile/' . $user->user_name);
        $avatarUrl = $user->getAvatarUrl();

        $data['user'] = $user;
        $data['page_title'] = __(':name - reviews from guests', ['name' => $user->getDisplayName()]);
        $data['followerCount'] = $followerCount;
        $data['followingCount'] = $followingCount;
        $data['profileUrl'] = $profileUrl;
        $data['avatarUrl'] = $avatarUrl;
        $data['followers'] = $followers;
        $data['following'] = $following;

        $data['breadcrumbs'] = [
            ['name' => $user->getDisplayName(), 'url' => route('user.profile', ['id' => $user->user_name ?? $user->id])],
            ['name' => __('Reviews from guests'), 'url' => ''],
        ];
        $this->registerCss('dist/frontend/module/user/css/profile.css');
        return view('User::frontend.profile.all-reviews', $data);
    }
    public function allServices(Request $request, $id_or_slug)
    {
        $all = get_bookable_services();
        $type = $request->query('type');
        if (empty($type) or !array_key_exists($type, $all)) {
            abort(404);
        }
        $moduleClass = $all[$type];
        $user = User::where('user_name', '=', $id_or_slug)->first();
        if (empty($user)) {
            $user = User::find($id_or_slug);
        }
        if (empty($user)) {
            abort(404);
        }
        $followerCount = DB::table('follow_member')
            ->where('follower_id', $user->id)
            ->count();

        $followingCount = DB::table('follow_member')
            ->where('user_id', $user->id)
            ->count();
        
            $followers = DB::table('follow_member')
            ->join('users', 'users.id', '=', 'follow_member.user_id')
            ->leftJoin('media_files', 'media_files.id', '=', 'users.avatar_id')
            ->where('follow_member.follower_id', $user->id)
            ->select('users.id', 'users.name', 'users.email', 'users.created_at','users.user_name', 'media_files.file_path')
            ->get();

        foreach ($followers as $follower) {
            $follower->avatar_url = $follower->file_path ? url('/uploads/' . $follower->file_path) : url('/images/avatar.png');
        }

        $following = DB::table('follow_member')
            ->join('users', 'users.id', '=', 'follow_member.follower_id')
            ->leftJoin('media_files', 'media_files.id', '=', 'users.avatar_id')
            ->where('follow_member.user_id', $user->id)
            ->select('users.id', 'users.name', 'users.email', 'users.created_at','users.user_name', 'media_files.file_path')
            ->get();

        foreach ($following as $follow) {
            $follow->avatar_url = $follow->file_path ? url('/uploads/' . $follow->file_path) : url('/images/avatar.png');
        }


        $profileUrl = url('/profile/' . $user->user_name);
        $avatarUrl = $user->getAvatarUrl();

        $data['user'] = $user;
        $data['page_title'] = __(':name - :type', ['name' => $user->getDisplayName(), 'type' => $moduleClass::getModelName()]);
        $data['breadcrumbs'] = [
            ['name' => $user->getDisplayName(), 'url' => route('user.profile', ['id' => $user->user_name ?? $user->id])],
            ['name' => __(':type by :first_name', ['type' => $moduleClass::getModelName(), 'first_name' => $user->first_name]), 'url' => ''],
        ];
        $data['type'] = $type;
        $data['followerCount'] = $followerCount;
        $data['followingCount'] = $followingCount;
        $data['profileUrl'] = $profileUrl;
        $data['avatarUrl'] = $avatarUrl;
        $data['followers'] = $followers;
        $data['following'] = $following;
        $data['services'] = $all[$type]::getVendorServicesQuery($user->id)->orderBy('id', 'desc')->paginate(6);
        $this->registerCss('dist/frontend/module/user/css/profile.css');
        return view('User::frontend.profile.all-services', $data);
    }
}
