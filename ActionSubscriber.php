<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
/**
 * Class Mailjet_Action_After_Submit
 * @see https://developers.elementor.com/custom-form-action/
 * Custom elementor form action after submit to add a subsciber to
 * Mailjet list via API 
 */
class Mailjet_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'mailjet';
	}

	/**
	 * Get Label
	 *
	 * Returns the action label
	 *
	 * @access public
	 * @return string
	 */
	public function get_label() {
		return __( 'Mailjet', 'text-domain' );
	}

	/**
	 * Run
	 *
	 * Runs the action after submit
	 *
	 * @access public
	 * @param \ElementorPro\Modules\Forms\Classes\Form_Record $record
	 * @param \ElementorPro\Modules\Forms\Classes\Ajax_Handler $ajax_handler
	 */
	public function run( $record, $ajax_handler ) {
		$settings = $record->get( 'form_settings' );


		//  Make sure that there is a Sendy list ID
		if ( empty( $settings['mailjet_list'] ) ) {
			return;
		}


		// Get sumitetd Form data
		$raw_fields = $record->get( 'fields' );

		// Normalize the Form Data
		$fields = [];
		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}


		// If we got this far we can start building our request data
		// Based on the param list at https://sendy.co/api
		$mailjet_data = [
			'Email' => $fields[ 'email' ],
			'Name' => $fields[ 'name' ],
		];

		$mailjet_data_step2 = [
			'Email' => $fields[ 'email' ],
			'Action' => 'addforce',
		];

		$mailjet_api = MAILJET_API;
		$mailjet_secret = MAILJET_SECRET;
		$auth = base64_encode( $mailjet_api . ':' . $mailjet_secret );

$mailjet_data_args = [
    'headers' => [
        'Authorization' => "Basic $auth"
    ],
    'body'    => $mailjet_data,
];      
$mailjet_data_step2_args = [
    'headers' => [
        'Authorization' => "Basic $auth"
    ],
    'body'    => $mailjet_data_step2,
];      

		// Send the request
		wp_remote_post( 'https://api.mailjet.com/v3/REST/contact', $mailjet_data_args );
		wp_remote_post( 'https://api.mailjet.com/v3/REST/contactslist/' . $settings['mailjet_list'] . '/managecontact', $mailjet_data_step2_args );

	}

	/**
	 * Register Settings Section
	 *
	 * Registers the Action controls
	 *
	 * @access public
	 * @param \Elementor\Widget_Base $widget
	 */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_mailjet',
			[
				'label' => __( 'Mailjet', 'text-domain' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'mailjet_list',
			[
				'label' => __( 'Mailjet List ID', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'separator' => 'before',
				'description' => __( 'the list id you want to subscribe a user to.', 'text-domain' ),
			]
		);


		$widget->end_controls_section();

	}

	/**
	 * On Export
	 *
	 * Clears form settings on export
	 * @access Public
	 * @param array $element
	 */
	public function on_export( $element ) {
		unset(
			$element['mailjet_list'],
		);
	}
}