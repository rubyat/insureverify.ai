<?php
namespace Modules\Template\Blocks;

class FyvHostOnboardingForm extends BaseBlock
{
    public function getName()
    {
        return __('FYV Host Onboarding Form');
    }

    public function getOptions()
    {
        return [
            'setting_tabs' => [
                'content' => [
                    'label' => __("Content"),
                    'icon'  => 'fa fa-pencil',
                    'order' => 1
                ],
                'style'   => [
                    'label' => __("Style"),
                    'order' => 2,
                    'icon'  => 'fa fa-object-group',
                ],
            ],
            'settings' => [
                // [
                //     'id'    => 'form_title',
                //     'type'  => 'input',
                //     'inputType' => 'text',
                //     'label' => __('Form Title'),
                //     'tab'   => 'content'
                // ],
                [
                    'id'    => 'title',
                    'type'  => 'input',
                    'inputType' => 'text',
                    'label' => __('Title'),
                    'tab'   => 'content'
                ],
                [
                    'id'    => 'sub_title',
                    'type'  => 'textArea',
                    'label' => __('Sub Title'),
                    'tab'   => 'content'
                ],
                [
                    'id'    => 'success_message',
                    'type'  => 'input',
                    'inputType' => 'text',
                    'label' => __('Success Message'),
                    'tab'   => 'content'
                ],
                [
                    'id'    => 'class',
                    'type'  => 'input',
                    'inputType' => 'text',
                    'label' => __('Wrapper Class (opt)'),
                    'tab'   => 'content'
                ],
                [
                    'id'    => 'padding',
                    'type'  => 'spacing',
                    'label' => __('Padding'),
                    'tab'   => 'style'
                ],
            ],
            'category'=>__("Form Block")
        ];
    }

    public function content($model = [])
    {
        $model['padding'] = $model['padding'] ?? [];
        return $this->view('Template::frontend.blocks.fyv-host-onboarding-form', $model);
    }
}
