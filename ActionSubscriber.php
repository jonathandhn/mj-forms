<?php
class Mailjet_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base
{

    public function get_name()
    {
        return 'mailjet';
    }

    public function get_label()
    {
        return __('Mailjet', 'text-domain');
    }

    public function run($record, $ajax_handler)
    {
        $settings = $record->get('form_settings');

        if (empty($settings['mailjet_list']))
        {
            return;
        }

        $raw_fields = $record->get('fields');

        $fields = [];
        foreach ($raw_fields as $id => $field)
        {
            $fields[$id] = $field['value'];
        }

        $mailjet_data = ['Email' => $fields['email'], 'Name' => $fields['name'], ];

        $mailjet_data_step2 = ['Email' => $fields['email'], 'Action' => 'addforce', ];

        $mailjet_api = MAILJET_API;
        $mailjet_secret = MAILJET_SECRET;
        $auth = base64_encode($mailjet_api . ':' . $mailjet_secret);

        $mailjet_data_args = ['headers' => ['Authorization' => "Basic $auth"], 'body' => $mailjet_data, ];
        $mailjet_data_step2_args = ['headers' => ['Authorization' => "Basic $auth"], 'body' => $mailjet_data_step2, ];

        wp_remote_post('https://api.mailjet.com/v3/REST/contact', $mailjet_data_args);
        wp_remote_post('https://api.mailjet.com/v3/REST/contactslist/' . $settings['mailjet_list'] . '/managecontact', $mailjet_data_step2_args);

    }

    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_mailjet', ['label' => __('Mailjet', 'text-domain') , 'condition' => ['submit_actions' => $this->get_name() , ], ]);

        $widget->add_control('mailjet_list', ['label' => __('Mailjet List ID', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('the list id you want to subscribe a user to.', 'text-domain') , ]);

        $widget->end_controls_section();

    }

    public function on_export($element)
    {
        unset($element['mailjet_list'],);
    }
}