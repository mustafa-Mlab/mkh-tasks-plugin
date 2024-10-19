<?php
/*
Plugin Name: MKH Tasks Plugin
Description: A simple task management plugin with CRUD functionality and REST API integration.
Version: 1.0
Author: Mustafa Kamal Hossain
Text Domain: mkh-tasks-plugin
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// constants
define( 'MKH_TASKS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MKH_TASKS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );


require_once MKH_TASKS_PLUGIN_PATH . 'includes/class-mkh-tasks.php';

function mkh_task_plugin_shortcode() {
    wp_enqueue_script(
        'mkh-react-app',
        plugin_dir_url(__FILE__) . 'assets/build/main.js',
        array(),
        null,
        true
    );

    if( is_user_logged_in() ) {
        $role = wp_get_current_user()->roles[0];
    }else{
        $role = 'guest';
    }
    
    wp_localize_script( 'mkh-react-app', 'taskPluginData', array(
        'rest_url' => esc_url_raw( rest_url() ),
        'nonce' => wp_create_nonce( 'wp_rest' ),
        'userRole' => $role,
    ));

    return '<div id="mkh-react-app"></div>';
}


add_shortcode('mkh_task_plugin', 'mkh_task_plugin_shortcode');

// Initialize the task class
function mkh_task_plugin_init() {
    new MKH_Tasks();
}
add_action( 'plugins_loaded', 'mkh_task_plugin_init' );
