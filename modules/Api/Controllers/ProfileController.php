<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profile(Request $request, $id_or_slug)
    {
        $user = User::where('user_name', '=', $id_or_slug)->first();
        if (empty($user)) {
            $user = User::find($id_or_slug);
        }
        if (empty($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }
        if ($user->role_id == 1) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        if (!$user->hasPermission('dashboard_vendor_access')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $data = [
            'user' => $user,
            'page_title' => $user->getDisplayName(),
        ];

        $all = get_bookable_services();
        $type = $request->query('type');

        if ($type && array_key_exists($type, $all)) {
            $moduleClass = $all[$type];
            $data['services'] = $moduleClass::getVendorServicesQuery($user->id)->orderBy('id', 'desc')->get();
        } else {
            $services = collect();
            foreach ($all as $serviceType => $moduleClass) {
                $services = $services->merge(
                    $moduleClass::getVendorServicesQuery($user->id)->orderBy('id', 'desc')->get()
                );
            }
            $data['services'] = $services->sortByDesc('id')->values();
        }

        return response()->json($data);
    }

    public function allReviews(Request $request, $id_or_slug)
    {
        $user = User::where('user_name', '=', $id_or_slug)->first();
        if (empty($user)) {
            $user = User::find($id_or_slug);
        }
        if (empty($user)) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $data = [
            'user' => $user,
            'page_title' => __(':name - reviews from guests', ['name' => $user->getDisplayName()]),
            'breadcrumbs' => [
                ['name' => $user->getDisplayName(), 'url' => route('user.profile', ['id' => $user->user_name ?? $user->id])],
                ['name' => __('Reviews from guests'), 'url' => ''],
            ]
        ];

        $data['reviews'] = $user->reviews()->paginate(10);

        return response()->json($data);
    }

    public function allServices(Request $request, $id_or_slug)
{
    $all = get_bookable_services();
    $type = $request->query('type');

    $user = User::where('user_name', '=', $id_or_slug)->first();
    if (empty($user)) {
        $user = User::find($id_or_slug);
    }
    if (empty($user)) {
        return response()->json(['message' => 'User not found'], 404);
    }

    $data = [
        'user' => $user,
        'page_title' => __(':name - Services', ['name' => $user->getDisplayName()]),
        'breadcrumbs' => [
            ['name' => $user->getDisplayName(), 'url' => route('user.profile', ['id' => $user->user_name ?? $user->id])],
            ['name' => __('All Services'), 'url' => ''],
        ],
    ];

    // If type is provided, get the specific type of services
    if ($type && array_key_exists($type, $all)) {
        $moduleClass = $all[$type];
        $servicesQuery = $moduleClass::getVendorServicesQuery($user->id)->orderBy('id', 'desc');
    } else {
        // Get services from all available types
        $servicesQuery = collect();
        foreach ($all as $serviceType => $moduleClass) {
            $servicesQuery = $servicesQuery->merge(
                $moduleClass::getVendorServicesQuery($user->id)->orderBy('id', 'desc')->get()
            );
        }
    }

    // Paginate the merged services collection
    $perPage = 6;
    $currentPage = Paginator::resolveCurrentPage();
    $currentPageResults = $servicesQuery->slice(($currentPage - 1) * $perPage, $perPage)->all();

    // Create a LengthAwarePaginator instance
    $servicesPaginated = new LengthAwarePaginator(
        $currentPageResults, 
        $servicesQuery->count(), 
        $perPage, 
        $currentPage, 
        ['path' => Paginator::resolveCurrentPath()]
    );

    // Add the paginated services to the data array
    $data['services'] = $servicesPaginated;

    return response()->json($data);
}

public function updateProfile(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $user = auth()->user();

// first_name
// last_name
// business_name
// email
// address
// address2
// phone
// birthday
// city
// state
// country
// zip_code
// bio
// website_url
// instagram_url
// facebook_url
// twitter_url
// linkedin_url

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:60',
                'last_name' => 'required|string|max:60',
                //'email' => 'nullable|email|unique:users,email,' . $user->id,
                'phone' => 'nullable|string|max:20',
                'address' => 'nullable|string|max:20',
                'address2' => 'nullable|string|max:20',
                'bio' => 'nullable|string',
                'website_url' => 'nullable|string',
                'instagram_url' => 'nullable|string',
                'facebook_url' => 'nullable|string',
                'twitter_url' => 'nullable|string',
                'linkedin_url' => 'nullable|string',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'errors' => $validator->errors(),
                ], 422);
            }
                    
            // Update fields
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            // $user->email = $request->email ?? $user->email;
            $user->phone = $request->phone ?? $user->phone;
            $user->address = $request->address ?? $user->address;
            $user->bio = $request->bio ?? $user->bio;
            $user->website_url = $request->website_url ?? $user->website_url;
            $user->instagram_url = $request->instagram_url ?? $user->instagram_url;
            $user->facebook_url = $request->facebook_url ?? $user->facebook_url;
            $user->twitter_url = $request->twitter_url ?? $user->twitter_url;
            $user->linkedin_url = $request->linkedin_url ?? $user->linkedin_url;
            
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                $mediaFile = new \Modules\Media\Models\MediaFile();
                if ($user->avatar_id) {
                    $oldMedia = $mediaFile->findById($user->avatar_id);
                    if ($oldMedia) {
                        \Storage::delete($oldMedia->file_path);
                         DB::table('media_files')->where('id', $oldMedia->id)->delete();
                    }
                }

                // Store new avatar
                $path = $request->file('avatar')->store('profile');
               
                $mediaFile->file_name = $request->file('avatar')->getClientOriginalName();
                $mediaFile->file_path = $path;
                $mediaFile->file_size = $request->file('avatar')->getSize();
                $mediaFile->file_type = $request->file('avatar')->getMimeType();
                $mediaFile->save();

                $user->avatar_id = $mediaFile->id;
               
            }

            $user->save();

            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully',
                 'user' => [
                     'id' => $user->id,
                     'first_name' => $user->first_name,
                     'last_name' => $user->last_name,
                     'business_name' => $user->business_name,
                     'name' => $user->name,
                     'email' => $user->email,
                     'phone' => $user->phone,
                     'bio' => $user->bio,
                     'website_url' => $user->website_url,
                     'instagram_url' => $user->instagram_url,
                     'facebook_url' => $user->facebook_url,
                     'twitter_url' => $user->twitter_url,
                     'linkedin_url' => $user->linkedin_url,
                     'avatar_url' => $user->getAvatarUrl(),
                 ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }
}
