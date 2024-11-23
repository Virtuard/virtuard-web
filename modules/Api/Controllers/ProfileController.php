<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;


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
}
