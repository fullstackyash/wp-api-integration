<?php
/**
 * Plugin settings file.
 *
 * @package Wp_Api_Integration
 */

namespace Wp_Api_Integration\Includes\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class for registering the plugin settings page.
 */
class Plugin_Settings {
	/**
	 * Page slug.
	 *
	 * @var $page_slug.
	 */
	private $page_slug = 'miusage-settings';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'miusage_api_custom_menu_page' ) );
		add_action( 'admin_init', array( $this, 'miusage_setting_fields' ) );
		add_action( 'admin_notices', array( $this, 'miusage_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'miusage_backend_script' ) );
		add_action( 'wp_ajax_miusage_refresh_data', array( $this, 'miusage_refresh_data' ) );
	}

	/**
	 * Register a custom menu page.
	 */
	public function miusage_api_custom_menu_page() {
		add_menu_page(
			__( 'Miusage Settings', 'wp-api-integration' ),
			__( 'Miusage Settings', 'wp-api-integration' ),
			'manage_options',
			$this->page_slug,
			array( $this, 'miusage_setting_menu_page' )
		);
	}

	/**
	 * Display a custom menu page.
	 */
	public function miusage_setting_menu_page() {
		?>
		<div class="wrap miusage-wrap">
			<h1><?php echo wp_kses_post( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php
					settings_fields( $this->page_slug ); // settings group name.
					do_settings_sections( $this->page_slug );
					submit_button( 'Save Settings', 'miusage_btn' );
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Creates settings fields for miusage settings page.
	 */
	public function miusage_setting_fields() {
		$option_group = 'miusage-settings';

		// 1. create section
		add_settings_section(
			'miusage_section_id', // section ID.
			'API Settings', // title.
			'', // callback function to display the section.
			$this->page_slug
		);

		// 2. register fields.
		register_setting( $option_group, 'miusage_refresh_api' );
		register_setting( $option_group, 'miusage_api_endpoint', 'sanitize_url' );

		// 3. add fields.
		add_settings_field(
			'miusage_refresh_api',
			'Refresh API data',
			array( $this, 'miusage_refresh_api_callback' ), // function to print the field.
			$this->page_slug,
			'miusage_section_id' // section ID.
		);

		add_settings_field(
			'miusage_api_endpoint',
			'Api End Point',
			array( $this, 'miusage_api_endpoint_callback' ),
			$this->page_slug,
			'miusage_section_id',
			array(
				'label_for'   => 'miusage_api_endpoint',
				'name'        => 'miusage_api_endpoint', // pass any custom parameters.
				'placeholder' => 'Enter API Endpoint URL',
			)
		);
	}

	/**
	 * Callback method for 'miusage_api_endpoint' settings field.
	 *
	 * @param array $args arguments for settings fields.
	 */
	public function miusage_api_endpoint_callback( $args ) {
		$api_url = get_option( $args['name'], 'https://miusage.com/v1/challenge/1/' );
		$api_url = ! empty( $api_url ) ? $api_url : 'https://miusage.com/v1/challenge/1/';
		printf(
			'<input type="text" id="%s" name="%s" value="%s" placeholder="%s" />',
			esc_attr( $args['name'] ),
			esc_attr( $args['name'] ),
			esc_url( $api_url ), // use default api if not provided.
			esc_attr( $args['placeholder'] )
		);
	}

	/**
	 * Callback method for 'miusage_refresh_api' settings field.
	 *
	 * @param array $args arguments for settings fields.
	 */
	public function miusage_refresh_api_callback( $args ) {
		$args;
		?>
			<label>
				<button type="button" id="miusage_refresh_api" class="miusage_btn miusage_refresh_api" name="miusage_refresh_api"><?php esc_html_e( 'Refresh', 'wp-api-integration' ); ?></button><span class="refresh_message"></span>
			</label>
		<?php
	}

	/**
	 * Callback to print refresh button on settings page.
	 */
	public function miusage_notice() {
		$page_name = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		$settings  = filter_input( INPUT_GET, 'settings-updated', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if (
			isset( $page_name )
			&& $this->page_slug === $page_name
			&& isset( $settings )
			&& true === $settings
		) {
			?>
				<div class="notice notice-success is-dismissible">
					<p>
						<strong><?php esc_html_e( 'Miusage settings saved.', 'wp-api-integration' ); ?></strong>
					</p>
				</div>
			<?php
		}
	}

	/**
	 * Enuqueue back-end scripts and styles.
	 *
	 * @param string $hook action name.
	 */
	public function miusage_backend_script( $hook ) {
		if ( 'toplevel_page_miusage-settings' !== $hook ) {
			return;
		}

		$plugin_name = basename( WP_API_INTEGRATION_DIR );
		wp_enqueue_script( 'miusage-backend-script', plugins_url( $plugin_name . '/src/scripts/miusage-admin.js' ), array(), filemtime( WP_API_INTEGRATION_DIR . '/src/scripts/miusage-admin.js' ), true );

		wp_localize_script(
			'miusage-backend-script',
			'ajaxload_params',
			array(
				'ajax_url' => site_url() . '/wp-admin/admin-ajax.php',
				'nonce'    => wp_create_nonce( 'ajax-nonce' ),
			)
		);

		// Enqueue style.
		wp_enqueue_style(
			'miusage-backend-style',
			plugins_url( $plugin_name . '/src/styles/miusage-admin.css' ),
			array(),
			filemtime( WP_API_INTEGRATION_DIR . '/src/styles/miusage-admin.css' )
		);
	}

	/**
	 * Ajax callback to refresh api data on button click in the admin settings page.
	 */
	public function miusage_refresh_data() {
		// Check nonce.
		if ( ! isset( $_POST['_ajaxnonce'] ) && ! wp_verify_nonce( sanitize_key( $_POST['_ajaxnonce'] ), 'ajax-load-event' ) ) {
			return false;

		}

		if ( delete_transient( 'miusage_api_data' ) ) {
			$msg    = 'The API data refreshed successfully.';
			$status = 'success';
		} else {
			$msg    = 'There is no data in database to refresh.';
			$status = 'error';
		}

		wp_send_json_success(
			array(
				'status' => $status,
				'msg'    => $msg,
			),
			200
		);
	}
}