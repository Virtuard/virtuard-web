<?php

namespace Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/home-page/service-counts",
     *     tags={"Home Page"},
     *     summary="Get service counts by category",
     *     description="Get the count of services available in each category (accommodation, property, business, etc.)",
     *     @OA\Response(
     *         response=200,
     *         description="Successfully retrieved service counts",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="category", type="string", example="accommodation"),
     *                     @OA\Property(property="service_type", type="string", example="hotel"),
     *                     @OA\Property(property="title", type="string", example="Accommodation"),
     *                     @OA\Property(property="count", type="integer", example=150)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getServiceCounts(Request $request)
    {
        // Get data from template home page (ListAllService block structure)
        $list = $this->getServiceCategories();
        $result = [];

        foreach ($list as $item) {
            // Get service type from link (more reliable)
            $serviceType = $this->getServiceTypeFromLink($item['link']);
            
            // Fallback to name if link doesn't work
            if (!$serviceType) {
                $serviceType = $this->getServiceTypeFromName($item['name']);
            }
            
            if ($serviceType) {
                $class = get_bookable_service_by_id($serviceType);
                
                if ($class && class_exists($class)) {
                    // Get count of published services
                    $count = $class::where('status', 'publish')->count();
                    
                    $result[] = [
                        'category' => $this->getCategoryName($serviceType),
                        'service_type' => $serviceType,
                        'title' => $item['name'],
                        'name' => $item['name'],
                        'image' => asset($item['image']),
                        'link' => $item['link'],
                        'count' => $count,
                    ];
                }
            }
        }

        return $this->sendSuccess($result);
    }

    /**
     * Get service categories from template structure (same as ListAllService block)
     */
    private function getServiceCategories()
    {
        return [
            [
                'name' => __('Accommodation'),
                'image' => 'uploads/images/accomodation.webp',
                'link' => route('hotel.search'),
            ],
            [
                'name' => __('Property'),
                'image' => 'uploads/images/property.webp',
                'link' => route('space.search'),
            ],
            [
                'name' => __('Commercial Activities'),
                'image' => 'uploads/images/business.webp',
                'link' => route('business.search'),
            ],
        ];
    }

    /**
     * Get service type from category name or link
     */
    private function getServiceTypeFromName($name)
    {
        // Map based on common patterns
        $nameLower = strtolower($name);
        
        if (strpos($nameLower, 'accommodation') !== false || strpos($nameLower, 'hotel') !== false) {
            return 'hotel';
        }
        if (strpos($nameLower, 'property') !== false || strpos($nameLower, 'space') !== false || strpos($nameLower, 'real estate') !== false) {
            return 'space';
        }
        if (strpos($nameLower, 'commercial') !== false || strpos($nameLower, 'business') !== false) {
            return 'business';
        }
        
        return null;
    }
    
    /**
     * Get service type from route link
     */
    private function getServiceTypeFromLink($link)
    {
        if (strpos($link, 'hotel') !== false) {
            return 'hotel';
        }
        if (strpos($link, 'space') !== false) {
            return 'space';
        }
        if (strpos($link, 'business') !== false) {
            return 'business';
        }
        
        return null;
    }

    /**
     * Map service type to category name
     */
    private function getCategoryName($serviceType)
    {
        $mapping = [
            'hotel' => 'accommodation',
            'space' => 'property',
            'business' => 'business',
            'boat' => 'vehicle',
            'car' => 'vehicle',
            'event' => 'event',
            'natural' => 'natural',
            'cultural' => 'cultural',
            'art' => 'art',
        ];

        return $mapping[$serviceType] ?? $serviceType;
    }

    /**
     * Get category title
     */
    private function getCategoryTitle($serviceType)
    {
        $mapping = [
            'hotel' => __('Accommodation'),
            'space' => __('Property'),
            'business' => __('Business'),
            'boat' => __('Vehicle'),
            'car' => __('Car'),
            'event' => __('Event'),
            'natural' => __('Natural'),
            'cultural' => __('Cultural'),
            'art' => __('Rendering'),
        ];

        return $mapping[$serviceType] ?? ucfirst($serviceType);
    }
}
