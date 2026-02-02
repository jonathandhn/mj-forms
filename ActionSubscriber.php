<?php

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Action_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MJF_Subscribe_Action_After_Submit extends Action_Base {

	public function get_name() {
		return 'MJF_Subscribe';
	}

	public function get_label() {
		return __( 'Mailjet Subscribe', 'mj-forms' );
	}

	public function run( $record, $ajax_handler ) {
		// Check for global constants
		if ( ! defined( 'MAILJET_API' ) || ! defined( 'MAILJET_SECRET' ) ) {
			error_log( 'MJ Forms: MAILJET_API or MAILJET_SECRET not defined.' );
			return;
		}

		$settings = $record->get( 'form_settings' );

		if ( empty( $settings['MJF_listID'] ) ) {
			return;
		}

		$raw_fields = $record->get( 'fields' );
		$fields     = [];

		foreach ( $raw_fields as $id => $field ) {
			$fields[ $id ] = $field['value'];
		}

		if ( empty( $fields['email'] ) ) {
			return;
		}

		$email     = $fields['email'];
		$name      = isset( $fields['name'] ) ? $fields['name'] : '';
		$firstname = isset( $fields['firstname'] ) ? $fields['firstname'] : '';
		$phone     = isset( $fields['phone'] ) ? $fields['phone'] : '';
		$custom    = isset( $fields['custom'] ) ? $fields['custom'] : '';

		// Prepare API Key
		$auth = base64_encode( MAILJET_API . ':' . MAILJET_SECRET );

		// Base Contact Data
		// Use 'force' to add even if unsubscribed previously, or just add. 'addforce' handles re-subscription.
		$mjf_data_contact      = [
			'Email' => $email,
		];
		$mjf_data_list_manage  = [
			'Email'  => $email,
			'Action' => 'addforce',
		];

		// Headers
		$headers = [
			'Authorization' => "Basic $auth",
			'Content-Type'  => 'application/json',
		];

		// 1. Ensure contact exists (optional explicit call, but good practice before list mgmt)
		wp_remote_post(
			'https://api.mailjet.com/v3/REST/contact',
			[
				'headers' => [ 'Authorization' => "Basic $auth" ], // Content-Type not strictly needed for GET/simple POST unless body json
				'body'    => $mjf_data_contact,
			]
		 );

		// 2. Add to list
		wp_remote_post(
			'https://api.mailjet.com/v3/REST/contactslist/' . $settings['MJF_listID'] . '/managecontact',
			[
				'headers' => [ 'Authorization' => "Basic $auth" ],
				'body'    => $mjf_data_list_manage,
			]
		);

		// 3. Update Contact Metadata
		// Prepare data batch if possible, or sequential updates
		$data_updates = [];

		if ( ! empty( $name ) && ! empty( $settings['MJF_name_field'] ) ) {
			$data_updates[] = [
				'Name'  => $settings['MJF_name_field'],
				'Value' => $name,
			];
		}
		if ( ! empty( $firstname ) && ! empty( $settings['MJF_firstname_field'] ) ) {
			$data_updates[] = [
				'Name'  => $settings['MJF_firstname_field'],
				'Value' => $firstname,
			];
		}
		if ( ! empty( $phone ) && ! empty( $settings['MJF_phone_field'] ) ) {
			$data_updates[] = [
				'Name'  => $settings['MJF_phone_field'],
				'Value' => $phone,
			];
		}
		if ( ! empty( $custom ) && ! empty( $settings['MJF_custom_field'] ) ) {
			$data_updates[] = [
				'Name'  => $settings['MJF_custom_field'],
				'Value' => $custom,
			];
		}

		if ( ! empty( $data_updates ) ) {
			$mjf_contact_data = [
				'Data' => $data_updates,
			];

			wp_remote_put(
				'https://api.mailjet.com/v3/REST/contactdata/' . $email,
				[
					'headers' => $headers,
					'body'    => json_encode( $mjf_contact_data ),
				]
			);
		}
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_MJF',
			[
				'label'     => __( 'Mailjet', 'mj-forms' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);

		$widget->add_control(
			'MJF_listID',
			[
				'label'       => __( 'Mailjet List ID', 'mj-forms' ),
				'type'        => Controls_Manager::TEXT,
				'separator'   => 'before',
				'description' => __( 'Insert the list id you want to subscribe a user to.', 'mj-forms' ),
			]
		);

		$widget->add_control(
			'MJF_firstname_field',
			[
				'label'       => __( 'Mailjet FirstName field', 'mj-forms' ),
				'type'        => Controls_Manager::TEXT,
				'separator'   => 'before',
				'description' => __( 'Set your mailjet firstname contact field name (e.g. "firstname").', 'mj-forms' ),
			]
		);

		$widget->add_control(
			'MJF_name_field',
			[
				'label'       => __( 'Mailjet LastName field', 'mj-forms' ),
				'type'        => Controls_Manager::TEXT,
				'separator'   => 'before',
				'description' => __( 'Set your mailjet lastname contact field name (e.g. "lastname").', 'mj-forms' ),
			]
		);

		$widget->add_control(
			'MJF_phone_field',
			[
				'label'       => __( 'Mailjet Phone field', 'mj-forms' ),
				'type'        => Controls_Manager::TEXT,
				'separator'   => 'before',
				'description' => __( 'Set your mailjet contact phone field name (e.g. "phone").', 'mj-forms' ),
			]
		);

		$widget->add_control(
			'MJF_custom_field',
			[
				'label'       => __( 'Mailjet Custom field', 'mj-forms' ),
				'type'        => Controls_Manager::TEXT,
				'separator'   => 'before',
				'description' => __( 'Set your own mailjet custom field name.', 'mj-forms' ),
			]
		);

		$widget->end_controls_section();
	}

	public function on_export( $element ) {
		unset(
			$element['MJF_listID'],
			$element['MJF_firstname_field'],
			$element['MJF_name_field'],
			$element['MJF_phone_field'],
			$element['MJF_custom_field']
		);
		return $element;
	}
}