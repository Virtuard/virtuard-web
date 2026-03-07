<?php
namespace Modules\Business\Blocks;

use Modules\Template\Blocks\BaseBlock;
use Modules\Core\Models\Terms;

class BusinessTermFeaturedBox extends BaseBlock
{
    public function getName()
    {
        return __('Business: Term Featured Box');
    }

    public function getOptions()
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
                    'id'           => 'term_business',
                    'type'         => 'select2',
                    'label'        => __('Select term business'),
                    'select2'      => [
                        'ajax'     => [
                            'url'      => route('business.admin.attribute.term.getForSelect2', ['type' => 'business']),
                            'dataType' => 'json'
                        ],
                        'width'    => '100%',
                        'multiple' => "true",
                    ],
                    'pre_selected' => route('business.admin.attribute.term.getForSelect2', [
                        'type'         => 'business',
                        'pre_selected' => 1
                    ])
                ],
            ],
            'category'=>__("Service Business")
        ];
    }

    public function content($model = [])
    {
        if (empty($term_business = $model['term_business'])) {
            return "";
        }
        $list_term = Terms::whereIn('id',$term_business)->with('translation')->get();
        $model['list_term'] = $list_term;
        return view('Business::frontend.blocks.term-featured-box.index', $model);
    }

    public function contentAPI($model = []){
        $model['list_term'] = null;
        if (!empty($term_business = $model['term_business'])) {
            $list_term = Terms::whereIn('id',$term_business)->get();
            if(!empty($list_term)){
                foreach ( $list_term as $item){
                    $model['list_term'][] = [
                        "id"=>$item->id,
                        "attr_id"=>$item->attr_id,
                        "name"=>$item->name,
                        "image_id"=>$item->image_id,
                        "image_url"=>get_file_url($item->image_id,"full"),
                        "icon"=>$item->icon,
                    ];
                }
            }
        }
        return $model;
    }
}
