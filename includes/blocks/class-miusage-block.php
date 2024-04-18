<?php
/**
 * Miusage block file.
 *
 * @package Wp_Api_Integration
 */

namespace Wp_Api_Integration\Includes\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for Miusage block.
 */
class Miusage_Block {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'miusage_block_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'miusage_enqueue_assets' ) );
	}

	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Behind the scenes, it registers also all assets so they can be enqueued
	 * through the block editor in the corresponding context.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	public function miusage_block_init(): void {
		register_block_type( WP_API_INTEGRATION__DIR__ . '/blocks/miusage-block/build' );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function miusage_enqueue_assets(): void {
		// Localize script.
		wp_localize_script(
			'create-block-miusage-block-editor-script',
			'ajaxload_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php', 'http' ),
				'nonce'    => wp_create_nonce( 'ajax-nonce' ),
			)
		);
	}
}
