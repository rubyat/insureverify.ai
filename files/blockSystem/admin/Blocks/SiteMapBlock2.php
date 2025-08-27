<?php
namespace Modules\Template\Blocks;

use Modules\Template\Blocks\BaseBlock;
use Modules\Media\Helpers\FileHelper;

class SiteMapBlock2 extends BaseBlock
{
    public function getName()
    {
        return __('Site Map Block');
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
                    'id'            => 'style',
                    'type'          => 'radios',
                    'label'         => __('Style'),
                    'values'        => [
                        [
                            'value'   => '',
                            'name' => __("Style 1")
                        ],
                        [
                            'value'   => 'style_2',
                            'name' => __("Style 2")
                        ],
                    ]
                ],
                [
                    'id'          => 'list_item',
                    'type'        => 'listItem',
                    'label'       => __('List Item(s)'),
                    'title_field' => 'title',
                    'settings'    => [
                        [
                            'id'        => 'title',
                            'type'      => 'input',
                            'inputType' => 'text',
                            'label'     => __('Title')
                        ],
                        [
                            'id'        => 'link_listing',
                            'type'      => 'textArea',
                            'label'     => __('Link Listing')
                        ],
                    ]
                ],
            ],
            'category'=>__("Other Block")
        ];
    }

    public function content($model = [])
    {
        if(($model['style'] ?? '') === 'style_2'){
            return $this->view('Template::frontend.blocks.site-map.style2', $model);
        }
        return $this->view('Template::frontend.blocks.site-map.index', $model);
    }

    public function contentAPI($model = []){
        return $model;
    }
}
