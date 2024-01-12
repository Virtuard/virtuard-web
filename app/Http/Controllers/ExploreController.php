<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Business;
use App\Models\ProductCategory;
use Modules\Space\Models\Space;
use Modules\Car\Models\Car;
use Modules\Event\Models\Event;
use Modules\Flight\Models\Flight;
use Modules\Boat\Models\Boat;

class ExploreController extends Controller
{
    public function index(Request $request)
    {
        $searchTerm = $request->input('location');
        $keyword = $request->input('keyword');

        $business = Business::take(5)->get();

        $businessCategories = ProductCategory::where('type', 'business')->get();

        $hotels = Business::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $properties = Space::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $cars = Car::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $boats = Boat::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $events = Event::where('status', 'publish')
            ->where('address', 'like', '%' . $searchTerm . '%')
            ->orderBy('id', 'DESC')
            ->get();

        $flights = Flight::where('status', 'publish')
            ->where('code', 'like', '%' . $searchTerm . '%')
            ->orderBy('id', 'DESC')
            ->get();

        return view('explore.index', compact('business', 'businessCategories', 'hotels', 'properties', 'cars', 'events', 'flights', 'boats'));
    }
}
