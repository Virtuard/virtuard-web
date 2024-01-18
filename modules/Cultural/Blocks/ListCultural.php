<?php
namespace Modules\Cultural\Blocks;

use Modules\Template\Blocks\BaseBlock;
use Modules\Cultural\Models\Cultural;
use Modules\Location\Models\Location;

class ListCultural extends BaseBlock
{

    protected $culturalClass;
    public function __construct(Cultural $culturalClass)
    {
        $this->culturalClass = $culturalClass;
    }

    public function getName()
    {
        return __('Cultural: List Items');
    }
    public function getOptions(): array
    {
        return [
            'settings' => [
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
                'id'        => 'number',
                'type'      => 'input',
                'inputType' => 'number',
                'label'     => __('Number Item')
            ],
            [
                'id'            => 'style',
                'type'          => 'radios',
                'label'         => __('Style'),
                'values'        => [
                    [
                        'value'   => 'normal',
                        'name' => __("Normal")
                    ],
                    [
                        'value'   => 'carousel',
                        'name' => __("Slider Carousel")
                    ]
                ]
            ],
            [
                'id'      => 'location_id',
                'type'    => 'select2',
                'label'   => __('Filter by Location'),
                'select2' => [
                    'ajax'  => [
                        'url'      => route('location.admin.getForSelect2'),
                        'dataType' => 'json'
                    ],
                    'width' => '100%',
                    'allowClear' => 'true',
                    'placeholder' => __('-- Select --')
                ],
                'pre_selected'=>route('location.admin.getForSelect2',['pre_selected'=>1])
            ],
            [
                'id'            => 'order',
                'type'          => 'radios',
                'label'         => __('Order'),
                'values'        => [
                    [
                        'value'   => 'id',
                        'name' => __("Date Create")
                    ],
                    [
                        'value'   => 'title',
                        'name' => __("Title")
                    ],
                ]
            ],
            [
                'id'            => 'order_by',
                'type'          => 'radios',
                'label'         => __('Order By'),
                'values'        => [
                    [
                        'value'   => 'asc',
                        'name' => __("ASC")
                    ],
                    [
                        'value'   => 'desc',
                        'name' => __("DESC")
                    ],
                ]
            ],
            [
                'type'=> "checkbox",
                'label'=>__("Only featured items?"),
                'id'=> "is_featured",
                'default'=>true
            ],
            [
                'id'           => 'custom_ids',
                'type'         => 'select2',
                'label'        => __('List by IDs'),
                'select2'      => [
                    'ajax'        => [
                        'url'      => route('cultural.admin.getForSelect2'),
                        'dataType' => 'json'
                    ],
                    'width'       => '100%',
                    'multiple'    => "true",
                    'placeholder' => __('-- Select --')
                ],
                'pre_selected' => route('cultural.admin.getForSelect2', [
                    'pre_selected' => 1
                ])
            ],
        ],
            'category'=>__("Service Cultural")
        ];
    }

    public function content($model = [])
    {
        $list = $this->query($model);
        $data = [
            'rows'       => $list,
            'style_list' => $model['style'],
            'title'      => $model['title'],
            'desc'       => $model['desc'],
        ];
        return view('Cultural::frontend.blocks.list-cultural.index', $data);
    }

    public function contentAPI($model = []){
        $rows = $this->query($model);
        $model['data']= $rows->map(function($row){
            return $row->dataForApi();
        });
        return $model;
    }

    public function query($model){
        $listCar = $this->culturalClass->search($model);
        $limit = $model['number'] ?? 5;
        return $listCar->paginate($limit);
    }
}
