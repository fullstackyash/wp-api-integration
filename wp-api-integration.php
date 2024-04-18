<?php
/**
 * Plugin Name:     WP API Integration
 * Plugin URI:      https://github.com/fullstackyash/wp-api-integration
 * Description:     This plugin fetches API data and uses it in a custom gutenberg block to showcase the data.
 * Author:          Yash Chopra
 * Author URI:     https://github.com/fullstackyash
 * Text Domain:     wp-api-integration
 * Domain Path:     /languages
 * Version:         1.0.0
 *
 * @package         Wp_Api_Integration
 */

namespace Wp_Api_Integration;

use WP_CLI;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! defined( 'WP_API_INTEGRATION_DIR' ) ) {
	define( 'WP_API_INTEGRATION_DIR', rtrim( plugin_dir_path( __FILE__ ), '/' ) );
}

if ( ! defined( 'WP_API_INTEGRATION__FILE__' ) ) {
	define( 'WP_API_INTEGRATION__FILE__', __FILE__ );
}

if ( ! defined( 'WP_API_INTEGRATION__DIR__' ) ) {
	define( 'WP_API_INTEGRATION__DIR__', __DIR__ );
}

/**
 * Autoloader.
 */
require_once WP_API_INTEGRATION_DIR . '/autoloader.php';

/**
 * Initialize the plugin.
 */
function initialize() {
	/**
	 * Plugin settings for API configurations in admin.
	 */
	new \Wp_Api_Integration\Includes\Admin\Plugin_Settings();

	/**
	 * API integration file.
	 */
	new \Wp_Api_Integration\Includes\Api\API();

	/**
	 * Register custom dynamic gutenberg block for displaying API data.
	 */
	new \Wp_Api_Integration\Includes\Blocks\Miusage_Block();

	/**
	 * Register CLI script for refreshing API data.
	 */
	if ( class_exists( 'WP_CLI' ) ) {
		$instance = new \Wp_Api_Integration\Includes\Cli\Miusage_Command();
		WP_CLI::add_command( 'miusage', $instance );
	}
}
initialize();
