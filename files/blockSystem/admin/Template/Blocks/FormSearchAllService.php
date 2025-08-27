<?php
namespace Themes\GoTrip\Template\Blocks;

use Modules\Flight\Models\SeatType;
use Modules\Media\Helpers\FileHelper;
use Modules\Location\Models\Location;

class FormSearchAllService extends \Modules\Template\Blocks\FormSearchAllService
{

    public function getOptions()
    {
        $list_service = [];
        foreach (get_bookable_services() as $key => $service) {
            $list_service[] = ['value'   => $key,
                'name' => ucwords($key)
            ];
        }
        $arg[] = [
            'id'            => 'service_types',
            'type'          => 'checklist',
            'listBox'          => 'true',
            'label'         => "<strong>".__('Service Type')."</strong>",
            'values'        => $list_service,
        ];

        $arg[] = [
            'id'        => 'title',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Title')
        ];
        $arg[] = [
            'id'        => 'sub_title',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Sub Title')
        ];

        $arg[] =  [
            'id'            => 'style',
            'type'          => 'radios',
            'label'         => __('Style Background'),
            'values'        => [
                [
                    'value'   => '',
                    'name' => __("Normal")
                ],
                [
                    'value'   => 'normal2',
                    'name' => __("Normal Ver 2")
                ],
                [
                    'value'   => 'carousel_v2',
                    'name' => __("Slider Ver 2")
                ],
                [
                    'value'   => 'carousel_v3',
                    'name' => __("Slider Carousel")
                ]
            ]
        ];

        $arg[] = [
            'id'    => 'bg_image',
            'type'  => 'uploader',
            'label' => __('- Layout Normal: Background Image Uploader')
        ];

        $arg[] = [
            'id'          => 'list_slider',
            'type'        => 'listItem',
            'label'       => __('- Layout Slider: List Item(s)'),
            'title_field' => 'title',
            'settings'    => [
                [
                    'id'    => 'bg_image',
                    'type'  => 'uploader',
                    'label' => __('Background Image Uploader')
                ]
            ]
        ];

        $arg[] = [
            'type'=> "checkbox",
            'label'=>__("Hide form search service?"),
            'id'=> "hide_form_search",
            'default'=>false
        ];


        $arg[] = [
            'id'    => 'villa_image',
            'type'  => 'uploader',
            'label' => __('Villa Icon')
        ];

        $arg[] = [
            'id'        => 'villa_title',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Villa Title')
        ];

        $arg[] = [
            'id'        => 'villa_title_review',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Villa total review')
        ];

        $arg[] = [
            'id'        => 'villa_review_from',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Villa review from')
        ];

        $arg[] = [
            'id'    => 'villa_button_image',
            'type'  => 'uploader',
            'label' => __('Villa Button Image')
        ];

        $arg[] = [
            'id'        => 'villa_button_link',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Villa button link')
        ];


        // car part

        $arg[] = [
            'id'    => 'car_image',
            'type'  => 'uploader',
            'label' => __('Car Icon')
        ];

        $arg[] = [
            'id'        => 'car_title',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Car Title')
        ];

        $arg[] = [
            'id'    => 'car_button_image',
            'type'  => 'uploader',
            'label' => __('Car Button Image')
        ];

        $arg[] = [
            'id'        => 'car_button_link',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Car button link')
        ];

        $arg[] = [
            'id'        => 'car_title_review',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Car total review')
        ];

        $arg[] = [
            'id'        => 'car_review_from',
            'type'      => 'input',
            'inputType' => 'text',
            'label'     => __('Car review from')
        ];

        return [
            'settings' => $arg,
            'category'=>__("Other Block")
        ];
    }

    public function content($model = [])
    {
        $model['bg_image_url'] = FileHelper::url($model['bg_image'] ?? "", 'full') ?? "";
        $model['villa_image_url'] = FileHelper::url($model['villa_image'] ?? "", 'full') ?? "";
        $model['villa_button_image_url'] = FileHelper::url($model['villa_button_image'] ?? "", 'full') ?? "";

        $model['car_image_url'] = FileHelper::url($model['car_image'] ?? "", 'full') ?? "";
        $model['car_button_image_url'] = FileHelper::url($model['car_button_image'] ?? "", 'full') ?? "";

        $model['list_location'] = $model['tour_location'] =  Location::where("status","publish")->limit(1000)->orderBy('name', 'asc')->with(['translation'])->get()->toTree();
        $model['style'] = $model['style'] ?? "";
        $model['list_slider'] = $model['list_slider'] ?? "";
        $model['modelBlock'] = $model;
        $model['seatType'] =  SeatType::get();
        $model['icons'] = [
            'car'    => 'icon-car',
            'hotel'  => 'icon-bed',
            'tour'   => 'icon-destination',
            'space'  => 'icon-ski',
            'event'  => 'icon-home',
            'boat'   => 'icon-yatch',
            'flight' => 'icon-tickets'
        ];

        return $this->view('Template::frontend.blocks.form-search-all-service.index', $model);
    }

}
