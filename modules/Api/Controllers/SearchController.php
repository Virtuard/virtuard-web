<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Booking\Models\Service;
use Modules\Flight\Controllers\FlightController;

class SearchController extends Controller
{

    /**
     * @OA\Get(
     *     path="/api/{type}/search",
     *     tags={"Service"},
     *     summary="Search for services",
     *     description="Type of service (hotel, space, business, car, event, natural, cultural, art.)",
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Type of service to search for",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="service_name",
     *         in="query",
     *         required=false,
     *         description="Name of service to search for",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         required=false,
     *         description="Number of items per page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful search response",
     *         @OA\JsonContent(
     *             @OA\Property(property="total", type="integer", example=100),
     *             @OA\Property(property="total_pages", type="integer", example=10),
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(type="object", 
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="object_model", type="string", example="hotel"),
     *                     @OA\Property(property="title", type="string", example="My Hotel"),
     *                     @OA\Property(property="price", type="number", format="float", example=50.00),
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request, missing required parameters",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Type is required")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found, type does not exist",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="Type does not exists")
     *         )
     *     )
     * )
     */

     public function search($type = '')
     {
         $type = $type ?: request()->get('type');
         
         if (empty($type)) {
             return $this->sendError(__("Type is required"));
         }
     
         $class = get_bookable_service_by_id($type);
         
         if (empty($class) || !class_exists($class)) {
             return $this->sendError(__("Type does not exist"));
         }
     
         $limit = request()->query('limit', setting_item($type . "_page_limit_item") ?: 9);
         $query = new $class();
     
         $rows = $query->search(request()->input())->paginate($limit);
     
         return $this->sendSuccess([
             'total' => $rows->total(),
             'per_page' => $rows->perPage(),
             'current_page' => $rows->currentPage(),
             'last_page' => $rows->lastPage(),
             'next_page_url' => $rows->nextPageUrl(),
             'prev_page_url' => $rows->previousPageUrl(),
             'data' => $rows->map(fn($row) => $row->dataForApi()),
         ]);
     }
     
    public function searchByAuthor(Request $request)
    {
        $author_id = $request->query('author_id');
        if (empty($author_id)) {
            return $this->sendError(__("Author ID is required"));
        }

        $allServices = get_bookable_services();
        $mergedData = collect();

        foreach ($allServices as $type => $class) {
            $query = new $class();
            $rows = $query->search(['author_id' => $author_id])->get();

            $filteredRows = $rows->filter(function ($item) use ($author_id) {
                return $item->author->id == $author_id;
            })->map(function ($item) use ($type) {
                $data = $item->dataForApi();
                $data['type'] = $type;
                return $data;
            });

            if ($filteredRows->isNotEmpty()) {
                $mergedData = $mergedData->merge($filteredRows);
            }
        }

        $total = $mergedData->count();

        return $this->sendSuccess([
            'total' => $total,
            'data' => $mergedData->values(),
        ]);
    }

    public function searchServices()
    {
        if (!empty(request()->query('limit'))) {
            $limit = request()->query('limit');
        } else {
            $limit = 9;
        }
        $query = new Service();
        $rows = $query->search(request()->input())->paginate($limit);
        $total = $rows->total();
        return $this->sendSuccess(
            [
                'total' => $total,
                'total_pages' => $rows->lastPage(),
                'data' => $rows->map(function ($row) {
                    return $row->dataForApi();
                }),
            ]
        );
    }

    public function getFilters($type = '')
    {
        $type = $type ? $type : request()->get('type');
        if (empty($type)) {
            return $this->sendError(__("Type is required"));
        }
        $class = get_bookable_service_by_id($type);
        if (empty($class) or !class_exists($class)) {
            return $this->sendError(__("Type does not exists"));
        }
        $data = call_user_func([$class, 'getFiltersSearch'], request());
        return $this->sendSuccess(
            [
                'data' => $data
            ]
        );
    }

    public function getFormSearch($type = '')
    {
        $type = $type ? $type : request()->get('type');
        if (empty($type)) {
            return $this->sendError(__("Type is required"));
        }
        $class = get_bookable_service_by_id($type);
        if (empty($class) or !class_exists($class)) {
            return $this->sendError(__("Type does not exists"));
        }
        $data = call_user_func([$class, 'getFormSearch'], request());
        return $this->sendSuccess(
            [
                'data' => $data
            ]
        );
    }

    /**
     * @OA\Get(
     *     path="/api/{type}/detail/{id}",
     *     tags={"Service"},
     *     summary="Get service detail",
     *     description="Retrieve details of a specific service by type and ID.",
     *     @OA\Parameter(
     *         name="type",
     *         in="path",
     *         required=true,
     *         description="Type of service (hotel, space, business, car, event, natural, cultural, art.)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the service",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="object_model", type="string", example="hotel"),
     *                 @OA\Property(property="name", type="string", example="My Hotel"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=0),
     *             @OA\Property(property="message", type="string", example="service not found")
     *         )
     *     )
     * )
     */
    public function detail($type = '', $id = '')
    {
        if (empty($type)) {
            return $this->sendError(__("Type is not available"))->setStatusCode(404);
        }
        if (empty($id)) {
            return $this->sendError(__("Resource ID is not available"))->setStatusCode(404);
        }

        $class = get_bookable_service_by_id($type);
        if (empty($class) or !class_exists($class)) {
            return $this->sendError(__("Type does not exists"))->setStatusCode(404);
        }

        $row = $class::find($id);
        if (empty($row)) {
            return $this->sendError(__("Resource not found"))->setStatusCode(404);
        }

        if ($type == 'flight') {
            return app()->make(FlightController::class)->getData(\request(), $id);
        }

        return $this->sendSuccess([
            'data' => $row->dataForApi(true)
        ]);
    }

    public function getIpanorama($type, $id)
    {
        if ($type !== 'hotel') {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $idipanorama_id = DB::table('bravo_hotels')
            ->where('id', $id)
            ->value('ipanorama_id');

        if (!$idipanorama_id) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $ipanorama = DB::table('ipanoramas')
            ->where('id', $idipanorama_id)
            ->get();

        if (!$ipanorama) {
            return response()->json(['message' => "Not Found"], 404);
        }

        return response()->json($ipanorama);
    }

    public function checkAvailability(Request $request, $type = '', $id = '')
{
    if (empty($type)) {
        return $this->sendError(__("Resource is not available"));
    }
    if (empty($id)) {
        return $this->sendError(__("Resource ID is not available"));
    }

    $class = get_bookable_service_by_id($type);
    if (empty($class) or !class_exists($class)) {
        return $this->sendError(__("Type does not exist"));
    }

    $classAvailability = $class::getClassAvailability();
    $classAvailability = app()->make($classAvailability);

    $request->merge(['id' => $id]);

    if ($type == "hotel") {
        $request->merge(['hotel_id' => $id]);

        $availabilityResponse = $classAvailability->checkAvailability($request);
        
        $availabilityData = $availabilityResponse->getData(true);

        $roomIds = collect($availabilityData['rooms'] ?? [])->pluck('id');

        $rooms = DB::table('bravo_hotel_rooms')
            ->whereIn('id', $roomIds) 
            ->get()
            ->keyBy('id'); 

        if (!empty($availabilityData['rooms'])) {
            foreach ($availabilityData['rooms'] as &$room) {
                $roomId = $room['id']; 
                $room['priceDay'] = $rooms[$roomId]->price ?? 0; 
            }
        }

        return $this->sendSuccess($availabilityData);
    }

    return $classAvailability->loadDates($request);
}


    public function checkBoatAvailability(Request $request, $id = '')
    {
        if (empty($id)) {
            return $this->sendError(__("Boat ID is not available"));
        }
        $class = get_bookable_service_by_id('boat');
        $classAvailability = $class::getClassAvailability();
        $classAvailability = app()->make($classAvailability);
        $request->merge(['id' => $id]);
        return $classAvailability->availabilityBooking($request);
    }
}
