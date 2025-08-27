<?php
namespace Modules\Template\Blocks;

use Modules\Media\Helpers\FileHelper;
use Modules\Template\Blocks\BaseBlock;

class FeaturedImage extends BaseBlock
{

    public function getName()
    {
        return __('Featured Image');
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
                    'id'    => 'image_id',
                    'type'  => 'uploader',
                    'label' => __('Featured Image')
                ],
                [
                    'id'        => 'image_tag',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Image Tag')
                ],
            ],
            'category'=>__("Other Block")
        ];
    }

    public function content($model = [])
    {
        if(!empty($model['image_id'])){
            $model['image_url'] = get_file_url($model['image_id'] , 'full');
        }
        return $this->view('Template::frontend.blocks.featured-image.index', $model);
    }

    public function contentAPI($model = []){
        if(!empty($model['image_id'])){
            $model['image_url'] = get_file_url($model['image_id'] , 'full');
        }
        return $model;
    }
}
