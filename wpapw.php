<?php

/**
 *
 * Plugin Name:       WP Advanced Posts Widget
 * Plugin URI:        https://github.com/lakiAlex/wp-advanced-posts-widget
 * Description:       WP Advanced Posts Widget is a no fuss WordPress widget to showcase your latest or trending posts.
 * Version:           1.0.1
 * Author:            Lazar Momcilovic
 * Author URI:        https://github.com/lakiAlex/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpapw
 * Domain Path:       /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WPAPW_VERSION', '1.0.2' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpapw-activator.php
 */
function activate_wpapw() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-wpapw-activator.php';
	Wpapw_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpapw-deactivator.php
 */
function deactivate_wpapw() {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-wpapw-deactivator.php';
	Wpapw_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wpapw' );
register_deactivation_hook( __FILE__, 'deactivate_wpapw' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'inc/class-wpapw.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wpapw() {

	$plugin = new Wpapw();
	$plugin->run();

}
run_wpapw();
