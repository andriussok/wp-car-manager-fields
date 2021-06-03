<?php

if (!defined('ABSPATH')) { exit; }

// enqueue styles and scripts
function wpcmf_add_scripts() {
  // Main CSS
  // wp_enqueue_style('wpcmf-main-style', plugins_url() . '/wp-car-manager-fields/public/css/style.css');
  // Main JS
  wp_enqueue_script('wpcmf-main-script', plugins_url() . '/wp-car-manager-fields/public/js/main.js', array('jquery', 'wpcm_js_car_submission'),'1.0', true );
}
add_action('wp_enqueue_scripts', 'wpcmf_add_scripts');