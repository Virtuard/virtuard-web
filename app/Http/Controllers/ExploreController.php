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
        $search = [
            'location' => $request->search_location,
            'keyword' => $request->search_keyword,
            'radius' => $request->search_radius,
            'map_lat' => $request->search_lat,
            'map_lng' => $request->search_lng,
        ];

        $searchlistings = [
            'business' => $this->business->search($search),
            'space' => $this->space->search($search),
            'hotel' => $this->hotel->search($search),
            'boat' => $this->boat->search($search),
            'event' => $this->event->search($search),
            'natural' => $this->natural->search($search),
            'cultural' => $this->cultural->search($search),
            'art' => $this->art->search($search),
        ];

        $listings = [
            'business' => $searchlistings['business']->get(),
            'space' => $searchlistings['space']->get(),
            'hotel' => $searchlistings['hotel']->get(),
            'boat' => $searchlistings['boat']->get(),
            'event' => $searchlistings['event']->get(),
            'natural' => $searchlistings['natural']->get(),
            'cultural' => $searchlistings['cultural']->get(),
            'art' => $searchlistings['art']->get(),
        ];

        $listMaps = [];
        foreach($listings as $i => $listing) {
            foreach($listing as $j => $list) {
                if ($list->map_lat == null || $list->map_lng == null) continue;
                $listMaps[] = get_map_listing($i, $list);
            }
        }
        usort($listMaps, [$this, 'sortListing']);

        return view('explore.index', compact(
            'listings',
            'listMaps',
        ));
    }

    private function sortListing($a, $b) {
        return $a['created_at'] < $b['created_at'];
    }

    private function getListing($model, $search)
    {   
        $data = $model
                ->with([
                    'author' => function($query) {
                        $query->select('id','name');
                    },
                ])
                ->when(isset($search['location']), function ($query) use ($search) {
                    $query->where('address', 'like', "%{$search['location']}%");
                })
                ->when(isset($search['keyword']), function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search['keyword']}%");
                })
                ->where([
                    ['status', '=', 'publish'],
                    ['map_lat', '!=', null],
                    ['map_lng', '!=', null],
                ])
                ->select(['author_id','title','slug','image_id','banner_image_id','status','address','map_lat','map_lng'])
                ->orderBy('id', 'DESC')
                ->get();

        return $data;
    }
}
