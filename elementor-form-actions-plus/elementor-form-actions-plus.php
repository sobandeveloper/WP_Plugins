<?php
/**
 * Plugin Name: Elementor Form Actions Plus
 * Description: Save Elementor form entries to a custom database, manage entries in admin panel, and display them via Elementor widget or shortcode.
 * Version: 1.0.0
 * Author: Soban Shahid
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! defined('EFAP_PLUGIN_DIR') ) {
    define('EFAP_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Debug test - check if file is even loading
error_log("EFAP: Main plugin file loaded");

// Includes
require_once EFAP_PLUGIN_DIR . 'includes/class-efap-loader.php';

// Debug test - check if loader file is included
error_log("EFAP: Loader file included");

// Initialize everything when plugins are loaded
add_action('plugins_loaded', function() {
    error_log("EFAP: plugins_loaded hook fired");
    EFAP_Loader::init();
});
