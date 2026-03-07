<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
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

    /**
     * @OA\Get(
     *     path="/api/{type}/search",
     *     tags={"Service"},
     *     summary="Search for services with filters",
     *     description="Search for services by type with filters (most viewed, top rated, etc.)",
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
     *     @OA\Parameter(
     *         name="order_by",
     *         in="query",
     *         required=false,
     *         description="Order by field (id, review_score, view_count, price, created_at)",
     *         @OA\Schema(type="string", example="review_score")
     *     ),
     *     @OA\Parameter(
     *         name="order",
     *         in="query",
     *         required=false,
     *         description="Order direction (asc, desc)",
     *         @OA\Schema(type="string", example="desc")
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         required=false,
     *         description="Filter type (most_viewed, top_rated, newest, cheapest, most_expensive)",
     *         @OA\Schema(type="string", example="top_rated")
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

        // Ensure Sanctum authentication is checked for wishlist
        // Sanctum will authenticate user from token even if route is not protected
        $user = request()->user('sanctum');
        if ($user) {
            // Set authenticated user for Auth facade to use in hasWishList relation
            Auth::setUser($user);
        }

        $limit = request()->query('limit', setting_item($type . "_page_limit_item") ?: 9);
        $query = new $class();

        $searchQuery = $query->search(request()->input());
        
        // Apply filters
        $searchQuery = $this->applyFilters($searchQuery, $type, request()->input());

        $rows = $searchQuery->paginate($limit);

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

    /**
     * Apply filters to search query
     */
    private function applyFilters($query, $type, $input)
    {
        $filter = $input['filter'] ?? '';
        $orderBy = $input['order_by'] ?? '';
        $order = $input['order'] ?? 'desc';

        // Apply filter shortcuts
        switch ($filter) {
            case 'most_viewed':
                // Check if view_count column exists in the table
                try {
                    $query->orderBy('view_count', 'desc');
                } catch (\Exception $e) {
                    // If view_count doesn't exist, fallback to default
                    $query->orderBy('id', 'desc');
                }
                break;
            case 'top_rated':
                $query->orderBy('review_score', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'cheapest':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'most_expensive':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            default:
                // Use custom order_by if provided
                if (!empty($orderBy)) {
                    $query->orderBy($orderBy, $order);
                } else {
                    // Default order
                    $query->orderBy('id', 'desc');
                }
                break;
        }

        return $query;
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


    public function searchByIdToken(Request $request)
    {
        $author_id = auth()->id();
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
    $limit = request()->query('limit', 9); 
    $types = ['hotel', 'space', 'business']; 
    $allData = collect(); 

    foreach ($types as $type) {
        $class = get_bookable_service_by_id($type);
        if (!empty($class) && class_exists($class)) {
            $query = new $class();
            $rows = $query->search(request()->input())->limit(3)->get();
            $allData = $allData->merge($rows->map(fn($row) => $row->dataForApi()));
        }
    }

    $allData = $allData->shuffle();

    $page = request()->query('page', 1);
    $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $allData->forPage($page, $limit), 
        $allData->count(),
        $limit,
        $page,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    return $this->sendSuccess([
        'total' => $paginated->total(),
        'total_pages' => $paginated->lastPage(),
        'data' => $paginated->items(),
    ]);
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

        // Ensure Sanctum authentication is checked for wishlist
        $user = request()->user('sanctum');
        if ($user) {
            // Set authenticated user for Auth facade to use in hasWishList relation
            Auth::setUser($user);
        }

        // Load with hasWishList relation (same as web detail controller)
        $row = $class::with(['hasWishList'])->find($id);
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
