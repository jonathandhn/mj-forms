<?php
/**
 * @package Mj Forms
 * @version 1.0.1
 */
/*
Plugin Name: Mj Forms
Plugin URI: https://github.com/jonathandhn/mj-forms
Description: Manage your Mailjet subscribers with elementor forms. 
Author: Jonathan DAHAN
Version: 1.0.1
Author URI: https://jonathanphoto.fr
Domain Path: /languages
*/
if (!defined('ABSPATH'))
{
    exit; // Exit if accessed directly
    
}
define('MJ_FORMS_PATH', plugin_dir_path(__FILE__));

add_action('elementor_pro/init', function ()
{
    // Here its safe to include our action class file
    include_once (MJ_FORMS_PATH . 'ActionSubscriber.php');

    // Instantiate the action class
    $MJF_action_subscribe = new MJF_Subscribe_Action_After_Submit();

    // Register the action with form widget
    \ElementorPro\Plugin::instance()
        ->modules_manager
        ->get_modules('forms')
        ->add_form_action($MJF_action_subscribe->get_name() , $MJF_action_subscribe);
});

