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

        if (empty($fields['email']))
        {
            return;
        }

        $MJF_data1 = ['Email' => $fields['email']];
        $MJF_data2 = ['Email' => $fields['email'], 'Action' => 'addforce'];
        $MJF_contactdata1 = ['Data' => [['Name' => $settings['MJF_name_field'], 'Value' => $fields['name'], ]]];
        $MJF_contactdata2 = ['Data' => [['Name' => $settings['MJF_firstname_field'], 'Value' => $fields['firstname'], ]]];
        $MJF_contactdata3 = ['Data' => [['Name' => $settings['MJF_phone_field'], 'Value' => $fields['phone'], ]]];

        $MJF_API = MAILJET_API;
        $MJF_SECRET = MAILJET_SECRET;
        $auth = base64_encode($MJF_API . ':' . $MJF_SECRET);

        $MJF_data1_args = ['headers' => ['Authorization' => "Basic $auth"], 'body' => $MJF_data1, ];
        $MJF_data2_args = ['headers' => ['Authorization' => "Basic $auth"], 'body' => $MJF_data2, ];
        $MJF_contactdata1_args = ['method' => 'PUT', 'headers' => array(
            'Authorization' => "Basic $auth",
            'Content-Type' => 'application/json'
        ) , 'body' => json_encode($MJF_contactdata1) , ];
        $MJF_contactdata2_args = ['method' => 'PUT', 'headers' => array(
            'Authorization' => "Basic $auth",
            'Content-Type' => 'application/json'
        ) , 'body' => json_encode($MJF_contactdata2) , ];
        $MJF_contactdata3_args = ['method' => 'PUT', 'headers' => array(
            'Authorization' => "Basic $auth",
            'Content-Type' => 'application/json'
        ) , 'body' => json_encode($MJF_contactdata3) , ];

        $MJF_responsecontact = wp_remote_post('https://api.mailjet.com/v3/REST/contact', $MJF_data1_args);
        $MJF_responsecontactslist = wp_remote_post('https://api.mailjet.com/v3/REST/contactslist/' . $settings['MJF_listID'] . '/managecontact', $MJF_data2_args);
        if ($fields['name'])
        {
            $MJF_responsecontactdata = wp_remote_post('https://api.mailjet.com/v3/REST/contactdata/' . $fields['email'], $MJF_contactdata1_args);
        }
        if ($fields['firstname'])
        {
            $MJF_responsecontactdata = wp_remote_post('https://api.mailjet.com/v3/REST/contactdata/' . $fields['email'], $MJF_contactdata2_args);
        }
        if ($fields['phone'])
        {
            $MJF_responsecontactdata = wp_remote_post('https://api.mailjet.com/v3/REST/contactdata/' . $fields['email'], $MJF_contactdata3_args);
        }
    }

    public function register_settings_section($widget)
    {
        $widget->start_controls_section('section_MJF', ['label' => __('Mailjet', 'text-domain') , 'condition' => ['submit_actions' => $this->get_name() , ], ]);

        $widget->add_control('MJF_listID', ['label' => __('Mailjet List ID', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('Insert the list id you want to subscribe a user to.', 'text-domain') , ]);

        $widget->add_control('MJF_firstname_field', ['label' => __('Mailjet FirstName field', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('Set your mailjet firstname contact field.', 'text-domain') , ]);

        $widget->add_control('MJF_name_field', ['label' => __('Mailjet LastName field', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('Set your mailjet lastname contact field.', 'text-domain') , ]);

        $widget->add_control('MJF_phone_field', ['label' => __('Mailjet Phone field', 'text-domain') , 'type' => \Elementor\Controls_Manager::TEXT, 'separator' => 'before', 'description' => __('Set your mailjet contact phone field.', 'text-domain') , ]);

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