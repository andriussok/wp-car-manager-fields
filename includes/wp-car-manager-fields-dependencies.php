<?php
/**
 * Verify if a plugin is active, if not deactivate the actual plugin an show an error
 * thanks dianjuar ( https://gist.github.com/dianjuar ) for the snippet
 * 
 * @param  [string]  $my_plugin_name
 * @param  [string]  $dependency_plugin_name
 * @param  [string]  $path_to_plugin (Format 'dependency_plugin/dependency_plugin.php')
 * @param  [string]  $textdomain
 * @param  [string]  $version_to_check
 */

function wpcmf_is_this_plugin_active($my_plugin_name, $dependency_plugin_name, $path_to_plugin, $textdomain = '', $version_to_check = null) {

  # Needed to the function "deactivate_plugins" works
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

  if( !is_plugin_active( $path_to_plugin ) )
  {
      # Deactivate the current plugin
    //   deactivate_plugins( plugin_basename( __FILE__ ) );
    deactivate_plugins( plugin_dir_path( __DIR__ ) . "wp-car-manager-fields.php" );

      # Show an error alert on the admin area
      add_action( 'admin_notices', function() use($my_plugin_name, $dependency_plugin_name, $textdomain)
      {
          ?>
          <div class="updated error">
              <p>
                  <?php
                  echo sprintf(
                      __( 'The plugin <strong>"%s"</strong> needs the plugin <strong>"%s"</strong> active', $textdomain ),
                      $my_plugin_name, $dependency_plugin_name
                  );
                  echo '<br>';
                  echo sprintf(
                      __( '<strong>%s </strong>has been deactivated', $textdomain ),
                      $my_plugin_name
                  );
                  ?>
              </p>
          </div>
          <?php
          if ( isset( $_GET['activate'] ) )
              unset( $_GET['activate'] );
      } );
  }
  else {

      # If version to check is not defined do nothing
      if($version_to_check === null)
          return;

      # Get the plugin dependency info
      $depPlugin_data = get_plugin_data( WP_PLUGIN_DIR.'/'.$path_to_plugin);

      # Compare version
      $error = !version_compare ( $depPlugin_data['Version'], $version_to_check, '>=') ? true : false;

      if($error) {

          # Deactivate the current plugin
          deactivate_plugins( plugin_basename( __FILE__ ) );

          add_action( 'admin_notices', function() use($my_plugin_name, $dependency_plugin_name, $version_to_check, $textdomain)
          {
              ?>
              <div class="updated error">
                  <p>
                      <?php
                      echo sprintf(
                          __( 'The plugin <strong>"%s"</strong> needs the <strong>version %s</strong> or newer of <strong>"%s"</strong>', $textdomain ),
                          $my_plugin_name,
                          $version_to_check,
                          $dependency_plugin_name
                      );
                      echo '<br>';
                      echo sprintf(
                          __( '<strong>%s </strong>has been deactivated', $textdomain ),
                          $my_plugin_name
                      );
                      ?>
                  </p>
              </div>
              <?php
              if ( isset( $_GET['activate'] ) )
                  unset( $_GET['activate'] );
          } );
      }
  }# else
}

// Run Test

$my_plugin_name = 'WP Car Manager Fields';
$dependency_plugin_name = 'WP Car Manager';
$path_to_plugin = 'wp-car-manager/wp-car-manager.php';

wpcmf_is_this_plugin_active($my_plugin_name, $dependency_plugin_name, $path_to_plugin,);