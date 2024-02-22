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
    protected $accomodation;
    protected $property;
    protected $vehicle;
    protected $business;
    protected $natural;
    protected $cultural;
    protected $art;
    protected $productCategory;

    public function __construct()
    {
        $this->accomodation = new Hotel();
        $this->property = new Space();
        $this->vehicle = new Boat();
        $this->business = new Business();
        $this->cultural = new Cultural();
        $this->natural = new Natural();
        $this->art = new Art();
        $this->productCategory = new ProductCategory();
    }

    public function index(Request $request)
    {
        $search = [
            'location' => $request->search_location,
            'keyword' => $request->search_keyword,
            'range' => $request->search_range,
        ];

        $topBusiness = $this->business->take(5)->get();
        $businessCategories = $this->productCategory->where('type', 'business')->get();

        $listings = [
            'business' => $this->getListing($this->business, $search),
            'properties' => $this->getListing($this->property, $search),
            'accomodations' => $this->getListing($this->accomodation, $search),
            'vehicles' => $this->getListing($this->vehicle, $search),
            'naturals' => $this->getListing($this->natural, $search),
            'culturals' => $this->getListing($this->cultural, $search),
            'arts' => $this->getListing($this->art, $search),
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
            'topBusiness',
            'businessCategories',
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
                ->when(isset($search['location']), function ($query) use ($search) {
                    $query->where('address', 'like', "%{$search['location']}%");
                })
                ->when(isset($search['title']), function ($query) use ($search) {
                    $query->where('keyword', 'like', "%{$search['keyword']}%");
                })
                ->where([
                    ['status', '=', 'publish'],
                    ['map_lat', '!=', null],
                    ['map_lng', '!=', null],
                ])
                ->orderBy('id', 'DESC')
                ->get();

        return $data;
    }
}
