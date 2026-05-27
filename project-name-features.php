<?php
/**
 * Project Name Features
 *
 * @package           Project_Name\Features
 * @author            rtCamp
 * @copyright         2026 rtCamp
 * @license           GPL-2.0-or-later
 *
 * Plugin Name:       Project Name Features
 * Plugin URI:        https://rtcamp.com
 * Description:       All backend functionality: post types, taxonomies, blocks, REST endpoints, settings, shortcodes, and custom roles.
 * Version:           1.0.0
 * Author:            rtCamp
 * Author URI:        https://rtcamp.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       project-name-features
 * Domain Path:       /languages
 * Requires PHP:      8.2
 * Requires at least: 6.7
 * Tested up to:      6.8
 */

declare( strict_types = 1 );

namespace Project_Name\Features;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Define the plugin constants.
 */
function constants(): void {
	/**
	 * File path to the plugin's main file.
	 */
	define( 'PROJECT_NAME_FEATURES_FILE', __FILE__ );

	/**
	 * Version of the plugin.
	 */
	define( 'PROJECT_NAME_FEATURES_VERSION', '1.0.0' );

	/**
	 * Root path to the plugin directory.
	 */
	define( 'PROJECT_NAME_FEATURES_PATH', plugin_dir_path( PROJECT_NAME_FEATURES_FILE ) );

	/**
	 * Root URL to the plugin directory.
	 */
	define( 'PROJECT_NAME_FEATURES_URL', plugin_dir_url( PROJECT_NAME_FEATURES_FILE ) );
}

constants();

// If autoloader fails, we cannot proceed.
require_once __DIR__ . '/inc/Autoloader.php';
if ( ! class_exists( 'Project_Name\Features\Autoloader' ) || ! \Project_Name\Features\Autoloader::autoload() ) {
	return;
}

// Load the main plugin class.
if ( class_exists( 'Project_Name\Features\Main' ) ) {
	\Project_Name\Features\Main::get_instance();
}
