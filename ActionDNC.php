<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
/**
 * Class Mailjet_Action_After_Submit
 * @see https://developers.elementor.com/custom-form-action/
 * Custom elementor form action after submit to add a subsciber to
 * Mailjet list via API 
 */
class MailjetDNC_Action_After_Submit extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	/**
	 * Get Name
	 *
	 * Return the action name
	 *
	 * @access public
	 * @return string
	 */
	public function get_name() {
		return 'mailjetDNC';
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
		return __( 'Mailjet DNC', 'text-domain' );
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
			'IsExcludedFromCampaigns' => 'true',
		];

		$mailjet_api = MAILJET_API;
		$mailjet_secret = MAILJET_SECRET;
		$auth = base64_encode( $mailjet_api . ':' . $mailjet_secret );

		$mailjet_data_args = [
			'headers' => [
				'Authorization' => "Basic $auth"
			],
			'body'    => json_encode($mailjet_data),
			'method'    => 'PUT'
		];        

		// Send the request
		wp_remote_request( 'https://api.mailjet.com/v3/REST/contact/' . $fields[ 'email' ], $mailjet_data_args );
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
				'label' => __( 'Mailjet DNC', 'text-domain' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);


		$widget->end_controls_section();

	}

}