<?php

/**
 *
 * @link              https://tareqmahmud.com
 * @since             1.0.0
 * @package           Cargohobe
 *
 * @wordpress-plugin
 * Plugin Name:       Cargohobe
 * Plugin URI:        https://tareqmahmud.com/cargohobe
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            MD. Tareq Mahmud
 * Author URI:        https://tareqmahmud.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cargohobe
 * Domain Path:       /languages
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
define( 'CARGOHOBE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cargohobe-activator.php
 */
function activate_cargohobe() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cargohobe-activator.php';
	Cargohobe_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cargohobe-deactivator.php
 */
function deactivate_cargohobe() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cargohobe-deactivator.php';
	Cargohobe_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cargohobe' );
register_deactivation_hook( __FILE__, 'deactivate_cargohobe' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cargohobe.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cargohobe() {

	$plugin = new Cargohobe();
	$plugin->run();

}
run_cargohobe();
