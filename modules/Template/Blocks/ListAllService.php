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
                'name' => 'Accomodation',
                'image' => 'uploads/demo/service/accomodation.jpg',
                'link' => route('hotel.search'),
            ],
            [
                'name' => 'Property',
                'image' => 'uploads/demo/service/property.jpg',
                'link' => route('space.search'),
            ],
            [
                'name' => 'Business',
                'image' => 'uploads/demo/service/business.jpg',
                'link' => route('business.search'),
            ],
            [
                'name' => 'Vehicle',
                'image' => '/uploads/demo/boat/boat-5.jpg',
                'link' => route('boat.search'),
            ],
            [
                'name' => 'Virtuard',
                'image' => '/uploads/0000/11/2023/07/20/virtuard.png',
                'link' => '/',
            ],
            [
                'name' => 'Event',
                'image' => '/uploads/demo/event/event-6.jpg',
                'link' => route('event.search'),
            ],
            [
                'name' => 'Cutural Heritage',
                'image' => 'uploads/demo/service/cultural.jpg',
                'link' => route('cultural.search'),
            ],
            [
                'name' => 'Natural and Landscape',
                'image' => 'uploads/demo/service/natural.jpg',
                'link' => route('natural.search'),
            ],
            [
                'name' => 'Rendering and Art',
                'image' => 'uploads/demo/service/art.jpg',
                'link' => route('art.search'),
            ],
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
