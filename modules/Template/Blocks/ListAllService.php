<?php
namespace Modules\Template\Blocks;

use Modules\Template\Blocks\BaseBlock;
use Modules\Location\Models\Location;

class ListAllService extends BaseBlock
{
    public function getOptions()
    {
        $list_service = [];
        foreach (get_bookable_services() as $key => $service) {
            $res = [
                    'value'   => $key,
                    'name' => ucwords($key)
            ];
            if (in_array($key, menu_listing())) {
                $list_service[] = $res;
            }
        }

        return [
            'settings' => [
                [
                    'id'            => 'service_type',
                    'type'          => 'checklist',
                    'listBox'          => 'true',
                    'label'         => "<strong>".__('Service Type')."</strong>",
                    'values'        => $list_service,
                ],
                [
                    'id'        => 'title',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Title')
                ],
                [
                    'id'        => 'desc',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Desc')
                ],
                [
                    'id'            => 'layout',
                    'type'          => 'radios',
                    'label'         => __('Style'),
                    'values'        => [
                        [
                            'value'   => 'style_1',
                            'name' => __("Style 1")
                        ],
                        [
                            'value'   => 'style_2',
                            'name' => __("Style 2")
                        ],
                        [
                            'value'   => 'style_3',
                            'name' => __("Style 3")
                        ],
                        [
                            'value'   => 'style_4',
                            'name' => __("Style 4")
                        ]
                    ]
                ],
                [
                    'type'=> "checkbox",
                    'label'=>__("Link to location detail page?"),
                    'id'=> "to_location_detail",
                    'default'=>false
                ]
            ],
            'category'=>__("Other Block")
        ];
    }

    public function getName()
    {
        return __('List All Service');
    }

    public function content($model = [])
    {
        $list = [
            [
                'name' => __('Accomodation'),
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
            // [
            //     'name' => __('Car'),
            //     'image' => 'uploads/demo/car/car-2.jpg',
            //     'link' => route('car.search'),
            // ],
            // [
            //     'name' => __('hide'),
            //     'image' => '/uploads/images/virtuard.png',
            //     'link' => '/',
            // ],
            // [
            //     'name' => __('Event'),
            //     'image' => 'uploads/images/event.webp',
            //     'link' => route('event.search'),
            // ],
            // [
            //     'name' => __('Cultural'),
            //     'image' => 'uploads/images/cultural.webp',
            //     'link' => route('cultural.search'),
            // ],
            // [
            //     'name' => __('Natural'),
            //     'image' => 'uploads/images/natural.webp',
            //     'link' => route('natural.search'),
            // ],
            // [
            //     'name' => __('Rendering'),
            //     'image' => 'uploads/images/art.webp',
            //     'link' => route('art.search'),
            // ],
        ];

        $data = [
            'rows'         => $list,
            'title'        => $model['title'],
            'desc'         => $model['desc'] ?? "",
            'service_type' => $model['service_type'],
            'layout'       => !empty($model['layout']) ? $model['layout'] : "style_1",
            'to_location_detail'=>$model['to_location_detail'] ?? ''
        ];
        return view('Template::frontend.blocks.list-all-service.index', $data);
    }
}
