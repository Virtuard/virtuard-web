<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Art\Models\Art;
use Modules\Space\Models\Space;
use Modules\Boat\Models\Boat;
use Modules\Business\Models\Business;
use Modules\Car\Models\Car;
use Modules\Cultural\Models\Cultural;
use Modules\Event\Models\Event;
use Modules\Hotel\Models\Hotel;
use Modules\Natural\Models\Natural;

class ExploreController extends Controller
{
    protected $hotel;
    protected $space;
    protected $boat;
    protected $car;
    protected $business;
    protected $natural;
    protected $cultural;
    protected $art;
    protected $event;

    public function __construct()
    {
        $this->hotel = new Hotel();
        $this->space = new Space();
        $this->boat = new Boat();
        $this->car = new Car();
        $this->business = new Business();
        $this->cultural = new Cultural();
        $this->natural = new Natural();
        $this->art = new Art();
        $this->event = new Event();
    }

    public function index(Request $request)
    {
        return view('app.explore.index');
    }

    public function searchService(Request $request)
    {
        $req = $request->all();
        $data = $this->formatSearch($req);

        $html = view('app.explore.partials.content', compact('data'))->render();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'html' => $html,
            'data' => $data,
        ]);
    }

    public function searchMap(Request $request)
    {
        $req = $request->all();

        $listings = [];
        $searchs = $this->getSearch($req);

        foreach ($searchs as $key => $search) {
            $listings[$key] = $searchs[$key]->get();
        }

        $data = [];
        foreach ($listings as $i => $listing) {
            foreach ($listing as $j => $list) {
                if ($list->map_lat == null || $list->map_lng == null) continue;
                $data[] = get_map_listing($i, $list);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $data,
        ]);
    }

    public function list(Request $request)
    {
        $req = $request->all();
        $data = $this->formatSearch($req);

        $html = view('app.explore.partials.content', compact('data'))->render();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'html' => $html,
            'data' => $data,
        ]);
    }

    private function formatSearch($req) {
        
        $paginate = 1;
        if(isset($req['service_type']) && $req['service_type'] != 'all') {
            $paginate = 4;
        }
        $listings = [];
        $searchs = $this->getSearch($req);

        foreach ($searchs as $key => $search) {
            $listings[$key] = $searchs[$key]->paginate($paginate);
        }

        $data = [];
        foreach ($listings as $i => $listing) {
            foreach ($listing as $j => $list) {
                $data[] = get_map_listing($i, $list);
            }
        }

        // Convert the array into a Laravel collection
        $collection = collect($data);

        // Sort the collection by 'created_at' in descending order
        $sorted = $collection->sortByDesc('created_at');

        // If you need the result as an array
        $data = $sorted->values()->all();

        return $data;
    }

    private function getSearch($req) {
        $type = $req['service_type'] ?? '';
        
        switch ($type) {
            case 'hotel':
                $searchs = ['hotel' => $this->hotel->search($req)];
                break;
            case 'space':
                $searchs = ['space' => $this->space->search($req)];
                break;
            case 'business':
                $searchs = ['business' => $this->business->search($req)];
                break;
            case 'boat':
                $searchs = ['boat' => $this->boat->search($req)];
                break;
            case 'car':
                $searchs = ['car' => $this->car->search($req)];
                break;
            case 'event':
                $searchs = ['event' => $this->event->search($req)];
                break;
            case 'natural':
                $searchs = ['natural' => $this->natural->search($req)];
                break;
            case 'cultural':
                $searchs = ['cultural' => $this->cultural->search($req)];
                break;
            case 'art':
                $searchs = ['art' => $this->art->search($req)];
                break;
            default:
                $searchs = [
                    'business' => $this->business->search($req),
                    'space' => $this->space->search($req),
                    'hotel' => $this->hotel->search($req),
                    // 'boat' => $this->boat->search($req),
                    'car' => $this->car->search($req),
                    'event' => $this->event->search($req),
                    'natural' => $this->natural->search($req),
                    'cultural' => $this->cultural->search($req),
                    'art' => $this->art->search($req),
                ];
        }

        return $searchs;
    }

    public function filter(Request $request)
    {
        $data = $request->filter;

        $html = view('app.explore.partials.content', compact('data'))->render();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'html' => $html,
            'data' => $data,
        ]);
    }
}
