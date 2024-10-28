<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
        $type = $type ? $type : request()->get('type');
        if (empty($type)) {
            return $this->sendError(__("Type is required"));
        }

        $class = get_bookable_service_by_id($type);
        if (empty($class) or !class_exists($class)) {
            return $this->sendError(__("Type does not exists"));
        }

        if (!empty(request()->query('limit'))) {
            $limit = request()->query('limit');
        } else {
            $limit = !empty(setting_item($type . "_page_limit_item")) ? setting_item($type . "_page_limit_item") : 9;
        }

        $query = new $class();
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
            return $this->sendError(__("Type does not exists"));
        }
        $classAvailability = $class::getClassAvailability();
        $classAvailability = app()->make($classAvailability);
        $request->merge(['id' => $id]);
        if ($type == "hotel") {
            $request->merge(['hotel_id' => $id]);
            return $classAvailability->checkAvailability($request);
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
