<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
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

class MapController extends Controller
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
            default:
                $searchs = [
                    'business' => $this->business->search($req),
                    'space' => $this->space->search($req),
                    'hotel' => $this->hotel->search($req)
                ];
        }

        return $searchs;
    }

    public function searchMapExplorerMobile(Request $request)
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

}
