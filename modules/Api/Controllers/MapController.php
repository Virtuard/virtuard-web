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

    /**
     * @OA\Get(
     *     path="/api/map/explore",
     *     tags={"Map"},
     *     summary="Get essential data for map explore",
     *     description="Get essential data for map explore feature including id, latitude, longitude, title, service type, and address",
     *     @OA\Parameter(
     *         name="service_type",
     *         in="query",
     *         description="Filter by service type (hotel, space, business, boat, car, natural, cultural, art, event). Leave empty for all types",
     *         required=false,
     *         @OA\Schema(type="string", example="hotel")
     *     ),
     *     @OA\Parameter(
     *         name="location_id",
     *         in="query",
     *         description="Filter by location ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="map_lat",
     *         in="query",
     *         description="Latitude for location-based search",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="map_lng",
     *         in="query",
     *         description="Longitude for location-based search",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved map explore data",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="latitude", type="string", example="-6.2088"),
     *                     @OA\Property(property="longitude", type="string", example="106.8456"),
     *                     @OA\Property(property="title", type="string", example="Grand Hotel"),
     *                     @OA\Property(property="service_type", type="string", example="hotel"),
     *                     @OA\Property(property="address", type="string", example="Jl. Sudirman No. 1, Jakarta"),
     *                     @OA\Property(property="image", type="string", example="https://example.com/uploads/image.jpg", description="Service image URL")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function explore(Request $request)
    {
        $req = $request->all();

        // Get all service types if not specified
        $serviceType = $req['service_type'] ?? '';
        
        $listings = [];
        $searchs = $this->getSearchAll($req, $serviceType);

        foreach ($searchs as $key => $search) {
            $listings[$key] = $searchs[$key]->get();
        }

        $data = [];
        foreach ($listings as $serviceTypeKey => $listing) {
            foreach ($listing as $list) {
                // Skip if no coordinates
                if (empty($list->map_lat) || empty($list->map_lng)) {
                    continue;
                }

                $data[] = [
                    'id' => $list->id,
                    'latitude' => $list->map_lat,
                    'longitude' => $list->map_lng,
                    'title' => $list->title ?? '',
                    'service_type' => $serviceTypeKey,
                    'address' => $list->address ?? '',
                    'image' => get_file_url($list->image_id ?? null, 'medium') ?? '',
                ];
            }
        }

        return $this->sendSuccess($data);
    }

    /**
     * Get search query for all service types
     */
    private function getSearchAll($req, $serviceType = '')
    {
        $searchs = [];

        if (!empty($serviceType)) {
            // Search specific service type
            switch ($serviceType) {
                case 'hotel':
                    $searchs['hotel'] = $this->hotel->search($req);
                    break;
                case 'space':
                    $searchs['space'] = $this->space->search($req);
                    break;
                case 'business':
                    $searchs['business'] = $this->business->search($req);
                    break;
                case 'boat':
                    if (method_exists($this->boat, 'search')) {
                        $searchs['boat'] = $this->boat->search($req);
                    }
                    break;
                case 'car':
                    if (method_exists($this->car, 'search')) {
                        $searchs['car'] = $this->car->search($req);
                    }
                    break;
                case 'natural':
                    if (method_exists($this->natural, 'search')) {
                        $searchs['natural'] = $this->natural->search($req);
                    }
                    break;
                case 'cultural':
                    if (method_exists($this->cultural, 'search')) {
                        $searchs['cultural'] = $this->cultural->search($req);
                    }
                    break;
                case 'art':
                    if (method_exists($this->art, 'search')) {
                        $searchs['art'] = $this->art->search($req);
                    }
                    break;
                case 'event':
                    if (method_exists($this->event, 'search')) {
                        $searchs['event'] = $this->event->search($req);
                    }
                    break;
            }
        } else {
            // Search all service types (default: hotel, space, business)
            $searchs = [
                'hotel' => $this->hotel->search($req),
                'space' => $this->space->search($req),
                'business' => $this->business->search($req),
            ];
            
            // Add other service types if they have search method
            if (method_exists($this->boat, 'search')) {
                $searchs['boat'] = $this->boat->search($req);
            }
            if (method_exists($this->car, 'search')) {
                $searchs['car'] = $this->car->search($req);
            }
            if (method_exists($this->natural, 'search')) {
                $searchs['natural'] = $this->natural->search($req);
            }
            if (method_exists($this->cultural, 'search')) {
                $searchs['cultural'] = $this->cultural->search($req);
            }
            if (method_exists($this->art, 'search')) {
                $searchs['art'] = $this->art->search($req);
            }
            if (method_exists($this->event, 'search')) {
                $searchs['event'] = $this->event->search($req);
            }
        }

        return $searchs;
    }

}
