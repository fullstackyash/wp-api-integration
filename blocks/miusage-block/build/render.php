<?php
/**
 * All of the parameters passed to the function where this file is being required are accessible in this scope:
 *
 * @param array    $attributes     The array of attributes for this block.
 * @param string   $content        Rendered block output. ie. <InnerBlocks.Content />.
 * @param WP_Block $block          The instance of the WP_Block class that represents the block being rendered.
 *
 * @package Wp_Api_Integration
 */

?>
<div <?php echo wp_kses_data( get_block_wrapper_attributes() ); ?> >
    <?php
    $wp_custom_api = new Wp_Api_Integration\Includes\Api\API();
    $api_data      = $wp_custom_api->get_api_data();
    $records       = $api_data->rows;
    $html          = '';
    ob_start();
    if ( ! empty( $records ) ) {
        ?>
		<div class="miusage-table">
			<div class="scrollable-div">
				<div class="table-headers row">
					<?php if ( ! empty( $attributes ) && $attributes['showId'] ) : ?>
						<div class="fieldset"><?php esc_html_e( 'ID', 'wp-api-integration' ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $attributes ) && $attributes['showFirstName'] ) : ?>
						<div class="fieldset"><?php esc_html_e( 'First Name', 'wp-api-integration' ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $attributes ) && $attributes['showLastName'] ) : ?>
						<div class="fieldset"><?php esc_html_e( 'Last Name', 'wp-api-integration' ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $attributes ) && $attributes['showEmail'] ) : ?>
						<div class="fieldset"><?php esc_html_e( 'Email', 'wp-api-integration' ); ?></div>
					<?php endif; ?>
					<?php if ( ! empty( $attributes ) && $attributes['showDate'] ) : ?>
						<div class="fieldset"><?php esc_html_e( 'Date', 'wp-api-integration' ); ?></div>
					<?php endif; ?>
				</div>
				<div class="table-details">
					<?php
					if ( ! empty( $records ) ) :
						foreach ( $records as $record ) :
							?>
							<div class="records row">
								<?php if ( ! empty( $attributes ) && $attributes['showId'] ) : ?>
									<div class="fieldset"><?php echo esc_html( $record->id ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $attributes ) && $attributes['showFirstName'] ) : ?>
									<div class="fieldset"><?php echo esc_html( $record->fname ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $attributes ) && $attributes['showLastName'] ) : ?>
									<div class="fieldset"><?php echo esc_html( $record->lname ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $attributes ) && $attributes['showEmail'] ) : ?>
									<div class="fieldset"><?php echo esc_html( $record->email ); ?></div>
								<?php endif; ?>
								<?php if ( ! empty( $attributes ) && $attributes['showDate'] ) : ?>
									<div class="fieldset"><?php echo esc_html( $record->date ); ?></div>
								<?php endif; ?>
							</div>
							<?php
						endforeach;
									endif;
					?>
				</div>
			</div>
		</div>
    <?php
    }
    $html = ob_get_clean();
    echo wp_kses_post( $html );
    if ( isset( $attributes['message'] ) ) {
        /**
         * The wp_kses_post function is used to ensure any HTML that is not allowed in a post will be escaped.
         *
         * @see https://developer.wordpress.org/reference/functions/wp_kses_post/
         * @see https://developer.wordpress.org/themes/theme-security/data-sanitization-escaping/#escaping-securing-output
         */
        echo wp_kses_post( $attributes['message'] . ' | ' . get_bloginfo( 'name' ) );
    }
?>
</div>