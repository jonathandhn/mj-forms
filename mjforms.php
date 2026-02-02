<?php
/**
 * Plugin Name: Mj Forms
 * Plugin URI: https://www.jonathan.dhn.one
 * Description: Manage your Mailjet subscribers with Elementor forms.
 * Author: Jonathan DAHAN
 * Author URI: https://www.jonathan.dhn.one
 * Version: 1.0.4
 * Text Domain: mj-forms
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

define( 'MJ_FORMS_PATH', plugin_dir_path( __FILE__ ) );

add_action( 'elementor_pro/init', function() {
	// Here its safe to include our action class file
	require_once MJ_FORMS_PATH . 'ActionSubscriber.php';

	// Instantiate the action class
	$mjf_action_subscribe = new \MJF_Subscribe_Action_After_Submit();

	// Register the action with form widget
	\ElementorPro\Plugin::instance()
		->modules_manager
		->get_modules( 'forms' )
		->add_form_action( $mjf_action_subscribe->get_name(), $mjf_action_subscribe );
} );

