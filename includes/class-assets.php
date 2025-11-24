<?php
/**
 * Handles the loading of styles and scripts for Delight Cookie Banner.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DCB_Assets {

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
	}

	public function enqueue_frontend_assets() {
		// Only load on frontend, never in admin area.
		if ( is_admin() ) {
			return;
		}

		// --- Styles ----------------------------------------------------------
		wp_enqueue_style(
			'dcb-style',
			DCB_URL . 'assets/css/style.css',
			[],
			DCB_VERSION
		);

		// --- Scripts ---------------------------------------------------------
		// Load in the header, since banner is rendered at wp_body_open.
		wp_enqueue_script(
			'dcb-banner',
			DCB_URL . 'assets/js/banner.js',
			[],
			DCB_VERSION,
			false // false = load in header; banner appears at wp_body_open
		);

		// --- Options ---------------------------------------------------------
		$options = get_option( 'dcb_settings', [] );

		$localized = [
			'text_accept'      => isset( $options['text_accept'] )
				? (string) $options['text_accept']
				: esc_html__( 'Accept', 'delight-cookie-banner' ),

			'text_reject'      => isset( $options['text_reject'] )
				? (string) $options['text_reject']
				: esc_html__( 'Reject', 'delight-cookie-banner' ),

			'text_message'     => isset( $options['text_message'] )
				? (string) $options['text_message']
				: esc_html__( 'We use cookies to improve your experience.', 'delight-cookie-banner' ),

			'privacy_link'     => isset( $options['privacy_link'] ) && is_string( $options['privacy_link'] )
				? esc_url( $options['privacy_link'] )
				: '#',

			'position'         => isset( $options['position'] ) && is_string( $options['position'] )
				? sanitize_key( $options['position'] )
				: 'bottom',

			'bg_color'         => isset( $options['bg_color'] ) ? sanitize_hex_color( $options['bg_color'] ) : '#ffffff',
			'text_color'       => isset( $options['text_color'] ) ? sanitize_hex_color( $options['text_color'] ) : '#000000',
			'btn_color'        => isset( $options['btn_color'] ) ? sanitize_hex_color( $options['btn_color'] ) : '#0073aa',
			'show_footer_link' => ! empty( $options['show_footer_link'] ),
		];

		wp_localize_script( 'dcb-banner', 'DCB_Settings', $localized );
	}
}
