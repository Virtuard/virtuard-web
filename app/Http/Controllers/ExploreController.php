<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Modules\Art\Models\Art;
use Modules\Space\Models\Space;
use Modules\Boat\Models\Boat;
use Modules\Business\Models\Business;
use Modules\Cultural\Models\Cultural;
use Modules\Hotel\Models\Hotel;
use Modules\Natural\Models\Natural;

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

        $business = Business::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $vehicles = Boat::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->where('title', 'like', '%' . $keyword . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $naturals = Natural::where('status', 'publish')
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

        $listings = [
            'business' => $business,
            'properties' => $properties,
            'accomodations' => $accomodations,
            'vehicles' => $vehicles,
            'naturals' => $naturals,
            'culturals' => $culturals,
            'arts' => $arts,
        ];

        $listMap = [];

        foreach($listings as $i => $listing) {
            foreach($listing as $j => $list) {
                if ($list->map_lat == null || $list->map_lng == null) continue;
                $listMap[] = get_map_listing($i, $list);
            }
        }

        return view('explore.index', compact(
            'topBusiness',
            'businessCategories',
            'listings',
            'listMap',
        ));
    }
}
