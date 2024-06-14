<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductCategory;
use Modules\Art\Models\Art;
use Modules\Space\Models\Space;
use Modules\Boat\Models\Boat;
use Modules\Business\Models\Business;
use Modules\Cultural\Models\Cultural;
use Modules\Event\Models\Event;
use Modules\Hotel\Models\Hotel;
use Modules\Natural\Models\Natural;

class ExploreController extends Controller
{
    protected $hotel;
    protected $space;
    protected $boat;
    protected $business;
    protected $natural;
    protected $cultural;
    protected $art;
    protected $event;
    protected $productCategory;

    public function __construct()
    {
        $this->hotel = new Hotel();
        $this->space = new Space();
        $this->boat = new Boat();
        $this->business = new Business();
        $this->cultural = new Cultural();
        $this->natural = new Natural();
        $this->art = new Art();
        $this->event = new Event();
        $this->productCategory = new ProductCategory();
    }

    public function index(Request $request)
    {
        $req = $request->all();
        $data = $this->formatSearch($req);

        $view = [
            'data' => $data,
        ];

        return view('explore.index', $view);
    }

    private function sortListing($a, $b)
    {
        return $a['created_at'] < $b['created_at'];
    }

    public function list(Request $request)
    {
        $req = $request->all();
        $data = $this->formatSearch($req);

        $html = view('explore.partials.content', compact('data'))->render();
        return response()->json([
            'status' => true,
            'message' => 'success',
            'html' => $html,
            'data' => $data,
        ]);
    }

    private function formatSearch($req) {
        $listings = [];
        $searchs = $this->getSearch($req);

        foreach ($searchs as $key => $search) {
            $listings[$key] = $searchs[$key]->paginate(50);
        }

        $data = [];
        foreach ($listings as $i => $listing) {
            foreach ($listing as $j => $list) {
                // if ($list->map_lat == null || $list->map_lng == null) continue;
                $data[] = get_map_listing($i, $list);
            }
        }

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
                    'boat' => $this->boat->search($req),
                    'event' => $this->event->search($req),
                    'natural' => $this->natural->search($req),
                    'cultural' => $this->cultural->search($req),
                    'art' => $this->art->search($req),
                ];
        }

        return $searchs;
    }
}
