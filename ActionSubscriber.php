<?php
class MJF_Subscribe_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base
{

    public function get_name()
    {
        return 'MJF_Subscribe';
    }

    public function get_label()
    {
        return __('Mailjet Subscribe', 'text-domain');
    }

    public function run($record, $ajax_handler)
    {
        $settings = $record->get('form_settings');

        if (empty($settings['MJF_listID']))
        {
            return;
        }

        $raw_fields = $record->get('fields');

        $fields = [];
        foreach ($raw_fields as $id => $field)
        {
            $fields[$id] = $field['value'];
        }

        $MJF_data = ['Email' => $fields['email']];
        $MJF_contactdata = [ 'Name' => $settings['MJF_name_field'], 'Value' => $fields['name'], 'Name' => $settings['MJF_firstname_field'], 'Value' => $fields['fname'],'Name' => $settings['MJF_phone_field'], 'Value' => $fields['phone'],];
        $MJF_data_step2 = ['Email' => $fields['email'], 'Action' => 'addforce', ];

        $MJF_API = MAILJET_API;
        $MJF_SECRET = MAILJET_SECRET;
        $MJF_auth = base64_encode($MJF_API . ':' . $MJF_SECRET);

        $MJF_data_args = ['headers' => ['Authorization' => "Basic  $MJF_auth"], 'body' =>    $MJF_data, ];
        $MJF_contactdata_args = ['headers' => ['Authorization' => "Basic  $MJF_auth"], 'body' =>    $MJF_contactdata, ];
        $MJF_data_step2_args = ['headers' => ['Authorization' => "Basic  $MJF_auth"], 'body' =>   $MJF_data_step2, ];

        $MJF_responsecontact = wp_remote_post('https://api.mailjet.com/v3/REST/contact',  $MJF_data_args);  
        $MJF_responsecontactdata = wp_remote_post('https://api.mailjet.com/v3/REST/contactdata/' . $fields['email'],  $MJF_contactdata_args);
        $MJF_responsecontactslist = wp_remote_post('https://api.mailjet.com/v3/REST/contactslist/' . $settings['MJF_listID'] . '/managecontact', $MJF_data_step2_args);

    }

    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_MJF', ['label' => __('Mailjet', 'text-domain') , 'condition' => ['submit_actions' => $this->get_name() , ], ]);

        $widget->add_control('MJF_listID', ['label' => __('Mailjet List ID', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('the list id you want to subscribe a user to.', 'text-domain') , ]);

        $widget->add_control('MJF_firstname_field', ['label' => __('Mailjet FirstName field', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('Name your mailjet firstname contact field.', 'text-domain') , ]);

        $widget->add_control('MJF_name_field', ['label' => __('Mailjet LastName field', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('Name your mailjet lastname contact field.', 'text-domain') , ]);

        $widget->add_control('MJF_phone_field', ['label' => __('Mailjet Phone field', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('Name your mailjet contact phone field.', 'text-domain') , ]);

        $widget->end_controls_section();

    }

    public function on_export($element)
    {
        unset($element['MJF_listID'],);
        unset($element['MJF_firstname_field'],);
        unset($element['MJF_name_field'],);
        unset($element['MJF_phone_field'],);
    }
}