<?php
/**
 * @package Mj Forms
 * @version 1.0.1
 */
/*
Plugin Name: Mj Forms
Plugin URI: https://jonathanphoto.fr
Description: Manage your Mailjet subscribers with elementor forms. 
Author: Jonathan DAHAN
Version: 1.0.0
Author URI: https://jonathanphoto.fr
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
define( 'MJ_FORM_PATH', plugin_dir_path( __FILE__ ) );


add_action( 'elementor_pro/init', function() {
	// Here its safe to include our action class file
	include_once( MJ_FORM_PATH . 'ActionSubscriber.php');

	// Instantiate the action class
	$mailjet_action = new Mailjet_Action_After_Submit();

	// Register the action with form widget
	\ElementorPro\Plugin::instance()->modules_manager->get_modules( 'forms' )->add_form_action( $mailjet_action->get_name(), $mailjet_action );
});