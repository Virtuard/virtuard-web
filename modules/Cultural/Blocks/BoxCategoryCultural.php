<?php
namespace Modules\Cultural\Blocks;

use Modules\Template\Blocks\BaseBlock;

use Modules\Media\Helpers\FileHelper;

use Modules\Cultural\Models\CulturalCategory;

class BoxCategoryCultural extends BaseBlock
{
    public function getOptions(){
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
                    'id'          => 'list_item',
                    'type'        => 'listItem',
                    'label'       => __('List Item(s)'),
                    'title_field' => 'title',
                    'settings'    => [
                        [
                            'id'      => 'category_id',
                            'type'    => 'select2',
                            'label'   => __('Select Category'),
                            'select2' => [
                                'ajax'  => [
                                    'url'      => route('cultural.admin.category.category.getForSelect2'),
                                    'dataType' => 'json'
                                ],
                                'width' => '100%',
                                'allowClear' => 'true',
                                'placeholder' => __('-- Select --')
                            ],
                            'pre_selected'=>route('cultural.admin.category.category.getForSelect2',['pre_selected'=>1])
                        ],
                        [
                            'id'    => 'image_id',
                            'type'  => 'uploader',
                            'label' => __('Image Background')
                        ],
                    ]
                ],
            ],
            'category'=>__("Service Cultural")
        ];
    }

    public function getName()
    {
        return __('Cultural: Box Category');
    }

    public function content($model = [])
    {
        if(!empty($model['list_item'])){
            $ids = collect($model['list_item'])->pluck('category_id');
            $categories = CulturalCategory::query()->whereIn("id",$ids)->where('status','publish')->get();
            $model['categories'] = $categories;
        }
        return view('Cultural::frontend.blocks.box-category-cultural.index', $model);
    }

    public function contentAPI($model = []){
        if(!empty($model['list_item'])){
            foreach ( $model['list_item'] as &$item ){
                $item['image_id_url'] = FileHelper::url($item['image_id'], 'full');
            }
        }
        return $model;
    }
}
