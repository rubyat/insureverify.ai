<?php
namespace Modules\Template\Blocks;

use Modules\Template\Blocks\BaseBlock;
use Modules\Media\Helpers\FileHelper;

class MobileQRcode extends BaseBlock
{
    public function getName()
    {
        return __('Mobile QR Code Block');
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
                    'id'    => 'qr_code_image',
                    'type'  => 'uploader',
                    'label' => __('QR Code Image')
                ],
                [
                    'id'        => 'link',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Link')
                ],
                [
                    'id'        => 'link_text',
                    'type'      => 'input',
                    'inputType' => 'text',
                    'label'     => __('Link Text')
                ],
            ],
            'category'=>__("Other Block")
        ];
    }

    public function content($model = [])
    {
        return $this->view('Template::frontend.blocks.mobile-qrcode.index', $model);
    }

    public function contentAPI($model = []){
        if(!empty($model['qr_code_image'])){
            $model['qr_code_image_url'] = FileHelper::url($model['qr_code_image'], 'full');
        }
        return $model;
    }
}
