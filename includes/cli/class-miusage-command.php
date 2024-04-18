<?php
/**
 * Miusage CLI file.
 *
 * @package Wp_Api_Integration.
 */

/**
 * Class to register CLI.
 */

namespace Wp_Api_Integration\Includes\Cli;

use WP_CLI;

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

/**
 * Class for registering the CLI.
 */
class Miusage_Command {
	/**
	 * Command callback function.
	 *
	 * @param array $args Array of arguments.
	 */
	public function refresh_data( $args ) {
		$args;
		if ( delete_transient( 'miusage_api_data' ) ) {
			WP_CLI::success( sprintf( 'The API data refreshed successfully.', ) );
		} else {
			WP_CLI::log( 'There is no data in database to refresh.' );
		}
	}
}
