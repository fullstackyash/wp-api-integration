<?php
/**
 * API file.
 *
 * @package Wp_Api_Integration
 */

namespace Wp_Api_Integration\Includes\Api;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for registering the custom API's.
 */
class API {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_get_api_data', array( $this, 'get_api_data' ) );
		add_action( 'wp_ajax_nopriv_get_api_data', array( $this, 'get_api_data' ) );
	}

	/**
	 * Function to fetch the API data.
	 */
	public function get_api_data() {
		if ( ! empty( $_POST ) && ! isset( $_POST['_ajaxnonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['_ajaxnonce'] ) ) ) {
			return false;
		}

		if ( get_transient( 'miusage_api_data' ) ) { // Check in database if API data exists.
			$data = get_transient( 'miusage_api_data' ); // If available fetch from database.
		} else {
			$api_url  = get_option( 'miusage_api_endpoint' );
			$api_url  = ! empty( $api_url ) ? $api_url : 'https://miusage.com/v1/challenge/1/';
			$response = wp_remote_get( $api_url ); //phpcs:ignore
			// Or else call API and fetch data.
			$body = json_decode( wp_remote_retrieve_body( $response ) );
			$data = $body->data;
			set_transient( 'miusage_api_data', $data, HOUR_IN_SECONDS ); // And Store in the database.
		}

		if ( ! empty( $_POST ) ) {
			wp_send_json_success( $data );
		} else {
			return $data;
		}
	}
}
