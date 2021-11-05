<?php
/**
 * Plugin Name:     Example Plugin
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     example-plugin
 * Domain Path:     /languages
 * Version:         0.1.0
 */

// If this file is called directly, abort.
defined( 'ABSPATH' ) || exit;

/**
 * Current plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'EXAMPLE_PLUGIN_VERSION', '1.0.0' );

if ( ! defined( 'EXAMPLE_PLUGIN_API_URL' ) ) {
	/**
	 * The API URL to check for updates.
	 * This should be unique to this plugin and point to a remote server
	 * running the Lemon Squeezy plugin.
	 */
	define( 'EXAMPLE_PLUGIN_API_URL', 'http://example.com/wp-json/lsq/v1' );
}

/**
 * Import the plugin updater class.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-plugin-updater.php';

/**
 * Instanciate the updater class.
 *
 * Everything within the updater is registered via hooks,
 * so it's safe to instanciate this at any time.
 */
new ExamplePluginUpdater(
	plugin_basename( __FILE__ ),
	plugin_basename( __DIR__ ),
	EXAMPLE_PLUGIN_VERSION,
	EXAMPLE_PLUGIN_API_URL
);
