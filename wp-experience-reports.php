<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wwdh.de
 * @since             1.0.0
 * @package           Wp_Experience_Reports
 *
 * @wordpress-plugin
 * Plugin Name:       Experience Reports
 * Plugin URI:        https://wwdh.de
 * Description:       Write Experience Reports and insert the reports in pages or posts. With the Experience Reports Gutenberg plugin you have countless setting options and can place the reports anywhere you want.
 * Version:           1.0.0
 * Author:            Jens Wiecker
 * Author URI:        https://wwdh.de
 * License:           GPL3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wp-experience-reports
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

const WP_EXPERIENCE_REPORTS_DB_VERSION = '1.0.0';
const WP_EXPERIENCE_REPORTS_PHP_VERSION = '7.4';
const WP_EXPERIENCE_REPORTS_WP_VERSION = '5.6';

//PLUGIN ROOT PATH
define('WP_EXPERIENCE_REPORTS_PLUGIN_DIR', dirname(__FILE__));
//PLUGIN SLUG
define('WP_EXPERIENCE_REPORTS_SLUG_PATH', plugin_basename(__FILE__));
define('WP_EXPERIENCE_REPORTS_BASENAME', plugin_basename(__DIR__));


include_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (is_plugin_active('post-selector/post-selector.php')) {
    $post_selector = true;
} else {
    $post_selector = false;
}

define('WP_EXPERIENCE_POST_SELECTOR_ACTIVE', $post_selector);

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-experience-reports-activator.php
 */
function activate_wp_experience_reports() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-experience-reports-activator.php';
	Wp_Experience_Reports_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-experience-reports-deactivator.php
 */
function deactivate_wp_experience_reports() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-experience-reports-deactivator.php';
	Wp_Experience_Reports_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wp_experience_reports' );
register_deactivation_hook( __FILE__, 'deactivate_wp_experience_reports' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-experience-reports.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */

global $wp_experience_reports_plugin;
$wp_experience_reports_plugin = new Wp_Experience_Reports();
$wp_experience_reports_plugin->run();
