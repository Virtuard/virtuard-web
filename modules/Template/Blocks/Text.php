<?php
namespace Modules\Template\Blocks;
class Text extends BaseBlock
{

    public function getName()
    {
        return __('Text');
    }

    public function getOptions()
    {
        return [
            'settings' => [
                [
                    'id'    => 'content',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label' => __('Description')
                ],
                [
                    'id'        => 'class',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Wrapper Class (opt)')
                ],
            ],
            'category'=>__("Other Block")
        ];
    }

    public function content($model = [])
    {
        return $this->view('Template::frontend.blocks.text', $model);
    }
}
