<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\ProductCategory;
use Modules\Art\Models\Art;
use Modules\Space\Models\Space;
use Modules\Car\Models\Car;
use Modules\Event\Models\Event;
use Modules\Flight\Models\Flight;
use Modules\Boat\Models\Boat;
use Modules\Cultural\Models\Cultural;
use Modules\Hotel\Models\Hotel;
use Modules\Tour\Models\Tour;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('location');
        $keyword = $request->input('keyword');

        $topBusiness = Business::take(5)->get();

        $businessCategories = ProductCategory::where('type', 'business')->get();

        $accomodations = Hotel::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $properties = Space::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $business = Car::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $vehicles = Boat::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $naturals = Tour::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $culturals = Cultural::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $arts = Art::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $listMap = [];

        foreach ($business as $val) {
            if ($val->map_lat == null || $val->map_lng == null) continue;
            $val = [
                'category' => 'business',
                'title' => $val->title,
                'map_lat' => $val->map_lat,
                'map_lng' => $val->map_lng,
                'icon' => asset('icon/shopping-bag.svg'),
            ];

            $listMap[] = $val;
        }

        foreach ($properties as $val) {
            if ($val->map_lat == null || $val->map_lng == null) continue;
            $val = [
                'category' => 'properties',
                'title' => $val->title,
                'map_lat' => $val->map_lat,
                'map_lng' => $val->map_lng,
                'icon' => asset('icon/house-user.svg'),
            ];

            $listMap[] = $val;
        }

        foreach ($accomodations as $val) {
            if ($val->map_lat == null || $val->map_lng == null) continue;
            $val = [
                'category' => 'accomodations',
                'title' => $val->title,
                'map_lat' => $val->map_lat,
                'map_lng' => $val->map_lng,
                'icon' => asset('icon/building.svg'),
            ];

            $listMap[] = $val;
        }

        foreach ($vehicles as $val) {
            if ($val->map_lat == null || $val->map_lng == null) continue;
            $val = [
                'category' => 'vehicles',
                'title' => $val->title,
                'map_lat' => $val->map_lat,
                'map_lng' => $val->map_lng,
                'icon' => asset('icon/directions-boat.svg'),
            ];

            $listMap[] = $val;
        }
        
        foreach ($naturals as $val) {
            if ($val->map_lat == null || $val->map_lng == null) continue;
            $val = [
                'category' => 'naturals',
                'title' => $val->title,
                'map_lat' => $val->map_lat,
                'map_lng' => $val->map_lng,
                'icon' => asset('icon/mountain.svg'),
            ];

            $listMap[] = $val;
        }

        foreach ($culturals as $val) {
            if ($val->map_lat == null || $val->map_lng == null) continue;
            $val = [
                'category' => 'culturals',
                'title' => $val->title,
                'map_lat' => $val->map_lat,
                'map_lng' => $val->map_lng,
                'icon' => asset('icon/church.svg'),
            ];

            $listMap[] = $val;
        }
        
        foreach ($arts as $val) {
            if ($val->map_lat == null || $val->map_lng == null) continue;
            $val = [
                'category' => 'arts',
                'title' => $val->title,
                'map_lat' => $val->map_lat,
                'map_lng' => $val->map_lng,
                'icon' => asset('icon/pencil-ruler.svg'),
            ];

            $listMap[] = $val;
        }

        return view('explore.index', compact(
            'topBusiness',
            'businessCategories',
            'business',
            'properties',
            'accomodations',
            'vehicles',
            'naturals',
            'culturals',
            'arts',
            'listMap',
        ));
    }
}
