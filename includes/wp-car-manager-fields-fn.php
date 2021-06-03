<?php

if (!defined('ABSPATH')) { exit; }

// BACKEND META-BOX
// Add option to add make-model in single car edit;

function gl_wpcm_modify_taxonomy() {
  // get the arguments of the already-registered taxonomy
  $wpcm_make_model_args = get_taxonomy( 'wpcm_make_model' ); // returns an object

  // make changes to the args
  // in this example there are three changes
  // again, note that it's an object
  $wpcm_make_model_args->show_ui = true;

  // re-register the taxonomy
  register_taxonomy( 'wpcm_make_model', 'wpcm_vehicle', (array) $wpcm_make_model_args );
}
// hook it up to 11 so that it overrides the original register_taxonomy function
add_action( 'init', 'gl_wpcm_modify_taxonomy', 11 );



// FRONTEND FORM
// Add option to add make-model in car submission form;

// Make

// register the ajax action for authenticated users
add_action('wp_ajax_add_new_make_fn', 'add_new_make_fn');

// register the ajax action for unauthenticated users
add_action('wp_ajax_nopriv_add_new_make_fn', 'add_new_make_fn');


// handle the ajax request
function add_new_make_fn() {
    $new_make_name = $_REQUEST['new_make_name'];
    // add your logic here...

    // Check only parent level, excluding child
    $make_ID = get_terms(  array(
      'taxonomy'    => 'wpcm_make_model',
      'hide_empty'  => false,
      'slug' => sanitize_title($new_make_name),
      'parent' => 0
    ));
    
    if(empty($make_ID)) {
      
      $new_make_ID = wp_insert_term($new_make_name, 'wpcm_make_model'); // Add new make term
      $make_obj = get_term_by('term_id', $new_make_ID['term_id'], 'wpcm_make_model');

      $make_data->id = $make_obj->term_id;
      $make_data->text = $make_obj->name;

      wp_send_json_success($make_data); // returns success json data

    } else {
      wp_send_json_error(); // on error, return error json data
    }
}


// Model

// register the ajax action for authenticated users
add_action('wp_ajax_add_new_model_fn', 'add_new_model_fn');

// register the ajax action for unauthenticated users
add_action('wp_ajax_nopriv_add_new_model_fn', 'add_new_model_fn');

// handle the ajax request
function add_new_model_fn() {
    $new_model_name = $_REQUEST['new_model_name'];
    $new_model_parent_ID = $_REQUEST['new_model_parent_ID'];

    // add your logic here...

    // Check if model exist under parent ID;
    $model_ID = get_terms(  array(
      'taxonomy'    => 'wpcm_make_model',
      'hide_empty'  => false,
      'slug' => sanitize_title($new_model_name),
      'parent' => $new_model_parent_ID
    ));

    if(empty($model_ID)) {
      
      // Add new model

      $new_model_ID = wp_insert_term( $new_model_name, 'wpcm_make_model',
        array(
          'parent' => $new_model_parent_ID
        )
      );

      $model_obj = get_term_by('term_id', $new_model_ID['term_id'], 'wpcm_make_model');

      $model_data->id = $model_obj->term_id;
      $model_data->text = $model_obj->name;

      wp_send_json_success($model_data); //returns success json data

    } else { 
      wp_send_json_error(); // on error, return error json data
    }
}