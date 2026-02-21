<?php

namespace App\Http\Controllers\v2\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use App\User;
use Modules\User\Events\UserVerificationSubmit;

class ProfileSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * View Profile Settings Page
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $data = [
            'page_title' => __('Profile Setting'),
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'business_name' => $user->business_name,
                'user_name' => $user->user_name,
                'email' => $user->email,
                'birthday' => $user->birthday ? date('Y-m-d', strtotime($user->birthday)) : null,
                'website_url' => $user->website_url,
                'instagram_url' => $user->instagram_url,
                'facebook_url' => $user->facebook_url,
                'twitter_url' => $user->twitter_url,
                'linkedin_url' => $user->linkedin_url,
                'bio' => $user->bio,
                'address' => $user->address,
                'address2' => $user->address2,
                'city' => $user->city,
                'state' => $user->state,
                'country' => $user->country,
                'zip_code' => $user->zip_code,
                'avatar_url' => $user->getAvatarUrl('full'),
                'is_verified' => $user->is_verified,
            ]
        ];

        return view('v2.vendor.profile.index', $data);
    }

    /**
     * Update Profile Data
     */
    public function update(Request $request)
    {
        if (is_demo_mode()) {
            return response()->json(['status' => false, 'message' => "Demo mode: disabled"], 403);
        }

        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'business_name' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'user_name' => [
                'required',
                'max:255',
                'min:4',
                'string',
                'alpha_dash',
                Rule::unique('users')->ignore($user->id)
            ],
            'birthday' => 'nullable|date',
            'website_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'linkedin_url' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:500',
            'address' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'zip_code' => 'required|string|max:50',
            'avatar_id' => 'nullable|integer',
        ], [
            'user_name.required' => __('Username is required.'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $input = $request->except(['bio', 'avatar_id']);
        $user->fill($input);

        $user->bio = clean($request->input('bio'));

        if ($request->filled('birthday')) {
            $user->birthday = date("Y-m-d", strtotime($request->input('birthday')));
        }

        $user->user_name = Str::slug($request->input('user_name'), "_");

        if ($request->filled('avatar_id')) {
            $user->avatar_id = $request->input('avatar_id');
        }

        $user->save();

        return response()->json([
            'status' => true,
            'message' => __('Profile updated successfully'),
            'user' => [
                'name' => $user->getDisplayName(),
                'avatar_url' => $user->getAvatarUrl()
            ]
        ]);
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        if (is_demo_mode()) {
            return response()->json(['status' => false, 'message' => "Demo mode: disabled"], 403);
        }

        $user = Auth::user();

        if (!Hash::check($request->input('current_password'), $user->password)) {
            return response()->json([
                'status' => false,
                'message' => __("Your current password does not match.")
            ], 422);
        }

        if (strcmp($request->input('current_password'), $request->input('new_password')) == 0) {
            return response()->json([
                'status' => false,
                'message' => __("New password cannot be the same as your current password.")
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed', // expects new_password_confirmation
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        $user->password = Hash::make($request->input('new_password'));
        $user->save();

        return response()->json([
            'status' => true,
            'message' => __('Password changed successfully!')
        ]);
    }

    /**
     * Account Verification (Upload ID, Phone)
     */
    public function verifyAccount(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'id_card_media_id' => 'required|integer',
            'phone' => 'required|string|max:50',
            // Phone formatting specific to frontend logic
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Based on the legacy code, we save this to user meta or specific columns
        $user->phone = $request->input('phone');

        // Save ID Card as meta
        $user->addMeta('verify_id_card', $request->input('id_card_media_id'));
        $user->addMeta('is_verified_id_card', 0); // Need admin approval

        // Notify admin about submission
        $user->verify_submit_status = 'new';
        $user->is_verified = 0; // Requires admin verification
        $user->save();

        event(new UserVerificationSubmit($user));

        return response()->json([
            'status' => true,
            'message' => __('Verification data submitted. Please wait for admin approval.')
        ]);
    }

    /**
     * Profile Preview
     */
    public function previewProfile(Request $request)
    {
        $user = Auth::user();

        return redirect()->route('member.show', ['userName' => $user->user_name]);
    }
}
