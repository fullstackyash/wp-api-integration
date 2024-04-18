<?php
/**
 * Autoloader file for plugin.
 *
 * @since      1.0.0
 * @package    Wp_Api_Integration
 */

namespace Wp_Api_Integration;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Auto loader function.
 *
 * @param string $resource Source namespace.
 *
 * @return void
 */
function autoloader( $resource = '' ) {
	$resource_path  = false;
	$namespace_root = 'Wp_Api_Integration\\';
	$resource       = trim( $resource, '\\' );

	if ( empty( $resource ) || strpos( $resource, '\\' ) === false || strpos( $resource, $namespace_root ) !== 0 ) {
		// Not our namespace, bail out.
		return;
	}

	// Remove our root namespace.
	$resource = str_replace( $namespace_root, '', $resource );

	$path = explode(
		'\\',
		str_replace( '_', '-', strtolower( $resource ) )
	);

	/**
	 * Time to determine which type of resource path it is,
	 * so that we can deduce the correct file path for it.
	 */
	if ( empty( $path[0] ) || empty( $path[1] ) ) {
		return;
	}

	$directory = '';
	$file_name = '';

	if ( 'includes' === $path[0] ) {

		if ( ! empty( $path[2] ) ) {
			$directory     = $path[1];
			$file_name     = sprintf( 'class-%s', trim( strtolower( $path[2] ) ) );
			$resource_path = sprintf( '%s/includes/%s/%s.php', untrailingslashit( WP_API_INTEGRATION_DIR ), $directory, $file_name );

		} else {
			$file_name     = sprintf( 'class-%s', trim( strtolower( $path[1] ) ) );
			$resource_path = sprintf( '%s/includes/%s.php', untrailingslashit( WP_API_INTEGRATION_DIR ), $file_name );

		}
	}

	/**
	 * If $is_valid_file has 0 means valid path or 2 means the file path contains a Windows drive path.
	 */
	$is_valid_file = validate_file( $resource_path );

	if ( ! empty( $resource_path ) && file_exists( $resource_path ) && ( 0 === $is_valid_file || 2 === $is_valid_file ) ) {
		// We already making sure that file is exists and valid.
		require_once( $resource_path ); // phpcs:ignore
	}
}

spl_autoload_register( '\Wp_Api_Integration\autoloader' );
