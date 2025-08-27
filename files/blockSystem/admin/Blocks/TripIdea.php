<?php
namespace Modules\Template\Blocks;

use Modules\Media\Helpers\FileHelper;

class TripIdea extends BaseBlock
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
                    'type'      => 'textArea',
                    'label'     => __('Desc')
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
                            'id'        => 'link',
                            'type'      => 'input',
                            'inputType' => 'text',
                            'label'     => __('Link')
                        ],
                        [
                            'id'    => 'desc',
                            'type'  => 'textArea',
                            'label' => __('Desc')
                        ],
                        [
                            'id'    => 'image',
                            'type'  => 'uploader',
                            'label' => __('Image')
                        ],
                    ]
                ],
            ],
            'category'=>__("Other Block")
        ];
    }

    public function getName()
    {
        return __('Trip Idea');
    }

    public function content($model = [])
    {
        if(!empty($model['list_item'])){
            foreach (  $model['list_item'] as &$item ){
                $item['image_url'] = isset($item['image']) ? FileHelper::url($item['image'], 'full') : 'images/no_image.png';
            }
        }

        return view('Template::frontend.blocks.trip-idea.index', $model);
    }

    public function contentAPI($model = []){
        if(!empty($model['list_item'])){
            foreach (  $model['list_item'] as &$item ){
                $item['image_url'] = isset($item['image']) ? FileHelper::url($item['image'], 'full') : 'images/no_image.png';
            }
        }
        return $model;
    }
}
