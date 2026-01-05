<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Profile",
 *     description="API Endpoints for user profile management"
 * )
 */
class ProfileController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/profile",
     *     tags={"Profile"},
     *     summary="Get current user profile",
     *     description="Retrieve the authenticated user's profile information",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *                 @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
     *                 @OA\Property(property="business_name", type="string", nullable=true, example="My Business"),
     *                 @OA\Property(property="bio", type="string", nullable=true, example="User bio"),
     *                 @OA\Property(property="address", type="string", nullable=true, example="123 Main St"),
     *                 @OA\Property(property="city", type="string", nullable=true, example="New York"),
     *                 @OA\Property(property="state", type="string", nullable=true, example="NY"),
     *                 @OA\Property(property="country", type="string", nullable=true, example="USA"),
     *                 @OA\Property(property="zip_code", type="string", nullable=true, example="10001"),
     *                 @OA\Property(property="website_url", type="string", nullable=true, example="https://example.com"),
     *                 @OA\Property(property="instagram_url", type="string", nullable=true, example="https://instagram.com/user"),
     *                 @OA\Property(property="facebook_url", type="string", nullable=true, example="https://facebook.com/user"),
     *                 @OA\Property(property="twitter_url", type="string", nullable=true, example="https://twitter.com/user"),
     *                 @OA\Property(property="linkedin_url", type="string", nullable=true, example="https://linkedin.com/in/user"),
     *                 @OA\Property(property="avatar_url", type="string", nullable=true, example="https://example.com/uploads/avatar.jpg"),
     *                 @OA\Property(property="user_name", type="string", nullable=true, example="johndoe"),
     *                 @OA\Property(property="birthday", type="string", format="date", nullable=true, example="1990-01-01")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid authentication token",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function getProfile(Request $request)
    {
        try {
            // Middleware auth:sanctum sudah memastikan user terautentikasi
            $user = $request->user();

            return response()->json([
                'status' => true,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'business_name' => $user->business_name,
                    'bio' => $user->bio,
                    'address' => $user->address,
                    'address2' => $user->address2,
                    'city' => $user->city,
                    'state' => $user->state,
                    'country' => $user->country,
                    'zip_code' => $user->zip_code,
                    'website_url' => $user->website_url,
                    'instagram_url' => $user->instagram_url,
                    'facebook_url' => $user->facebook_url,
                    'twitter_url' => $user->twitter_url,
                    'linkedin_url' => $user->linkedin_url,
                    'avatar_url' => $user->getAvatarUrl(),
                    'user_name' => $user->user_name,
                    'birthday' => $user->birthday,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong. Please try again later.',
            ], 500);
        }
    }

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

    /**
     * @OA\Post(
     *     path="/api/profile",
     *     tags={"Profile"},
     *     summary="Update user profile",
     *     description="Update the authenticated user's profile information",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"first_name", "last_name"},
     *                 @OA\Property(property="first_name", type="string", maxLength=60, example="John", description="User's first name"),
     *                 @OA\Property(property="last_name", type="string", maxLength=60, example="Doe", description="User's last name"),
     *                 @OA\Property(property="phone", type="string", maxLength=20, nullable=true, example="+1234567890", description="User's phone number"),
     *                 @OA\Property(property="address", type="string", maxLength=255, nullable=true, example="123 Main St", description="User's address"),
     *                 @OA\Property(property="address2", type="string", maxLength=255, nullable=true, example="Apt 4B", description="User's address line 2"),
     *                 @OA\Property(property="bio", type="string", nullable=true, example="User bio description", description="User's biography"),
     *                 @OA\Property(property="website_url", type="string", nullable=true, example="https://example.com", description="User's website URL"),
     *                 @OA\Property(property="instagram_url", type="string", nullable=true, example="https://instagram.com/user", description="User's Instagram URL"),
     *                 @OA\Property(property="facebook_url", type="string", nullable=true, example="https://facebook.com/user", description="User's Facebook URL"),
     *                 @OA\Property(property="twitter_url", type="string", nullable=true, example="https://twitter.com/user", description="User's Twitter URL"),
     *                 @OA\Property(property="linkedin_url", type="string", nullable=true, example="https://linkedin.com/in/user", description="User's LinkedIn URL"),
     *                 @OA\Property(property="avatar", type="string", format="binary", nullable=true, description="User's avatar image (jpeg, png, jpg, max 2MB)")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Profile updated successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="first_name", type="string", example="John"),
     *                 @OA\Property(property="last_name", type="string", example="Doe"),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
     *                 @OA\Property(property="phone", type="string", nullable=true, example="+1234567890"),
     *                 @OA\Property(property="business_name", type="string", nullable=true, example="My Business"),
     *                 @OA\Property(property="bio", type="string", nullable=true, example="User bio"),
     *                 @OA\Property(property="website_url", type="string", nullable=true, example="https://example.com"),
     *                 @OA\Property(property="instagram_url", type="string", nullable=true, example="https://instagram.com/user"),
     *                 @OA\Property(property="facebook_url", type="string", nullable=true, example="https://facebook.com/user"),
     *                 @OA\Property(property="twitter_url", type="string", nullable=true, example="https://twitter.com/user"),
     *                 @OA\Property(property="linkedin_url", type="string", nullable=true, example="https://linkedin.com/in/user"),
     *                 @OA\Property(property="avatar_url", type="string", nullable=true, example="https://example.com/uploads/avatar.jpg")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Missing or invalid authentication token",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Unauthorized")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\AdditionalProperties(type="array", @OA\Items(type="string"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Something went wrong. Please try again later.")
     *         )
     *     )
     * )
     */
public function updateProfile(Request $request)
    {
        try {
            // Middleware auth:sanctum sudah memastikan user terautentikasi
            $user = $request->user();


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
                        Storage::delete($oldMedia->file_path);
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
