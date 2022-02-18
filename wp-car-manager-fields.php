<?php
/*
    Plugin Name: WP Car Manager Fields
    Plugin URI: https://github.com/andriussok/wp-car-manager-fields
    Description: Add new make-model helper. Plugin adds make-model metabox in single car admin and allows to add new make-model in frontend car-submission form.
    Version: 1.0.0
    Author: Andrius Sok.
    Author URI: https://github.com/andriussok
    License: GPL v2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    // header( 'Status: 403 Forbidden', true, 403 );
    exit; // Exit if accessed directly
}

// Check dependencies
require_once(plugin_dir_path(__FILE__).'includes/wp-car-manager-fields-dependencies.php');

// Add scripts
require_once(plugin_dir_path(__FILE__).'includes/wp-car-manager-fields-scripts.php');

// Add Functions
require_once(plugin_dir_path(__FILE__).'includes/wp-car-manager-fields-fn.php');
