<?php
/**
 * Handles the frontend rendering for Delight Cookie Banner.
 * Always renders banner (JS controls visibility).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DCB_Frontend {

	public function __construct() {
		add_action( 'wp_footer', [ $this, 'render_cookie_banner' ] );
		add_action( 'wp_footer', [ $this, 'render_footer_link' ], 999 );
		add_shortcode( 'dcb_open', [ $this, 'shortcode_open' ] );
		add_shortcode( 'dcb_reset', [ $this, 'shortcode_reset' ] );
	}

	/**
	 * Render the cookie banner markup.
	 */
public function render_cookie_banner() {

	$defaults = ( new DCB_Admin() )->get_default_settings();
	$options  = wp_parse_args( get_option( 'dcb_settings', [] ), $defaults );

	$position     = sanitize_key( $options['position'] ?? 'bottom' );
	$text_msg     = wp_kses_post( $options['text_message'] ?? '' );
	$text_accept  = sanitize_text_field( $options['text_accept'] ?? '' );
	$text_reject  = sanitize_text_field( $options['text_reject'] ?? '' );
	$show_reject  = ! empty( $options['show_reject_button'] ); // 👈 neues Feld

	// Privacy link (fallback to WP privacy policy)
	$privacy_id  = ! empty( $options['privacy_page'] )
		? absint( $options['privacy_page'] )
		: get_option( 'wp_page_for_privacy_policy' );

	$privacy_url   = $privacy_id ? get_permalink( $privacy_id ) : '#';
	$privacy_title = $privacy_id
		? get_the_title( $privacy_id )
		: esc_html__( 'Privacy Policy', 'delight-cookie-banner' );

	// CSS variables
	$vars = [
		'--dcb-bg-color'            => sanitize_hex_color( $options['bg_color'] ?? '#ffffff' ),
		'--dcb-text-color'          => sanitize_hex_color( $options['text_color'] ?? '#000000' ),
		'--dcb-accept-bg'           => sanitize_hex_color( $options['btn_accept_bg'] ?? '#0073aa' ),
		'--dcb-accept-text'         => sanitize_hex_color( $options['btn_accept_text'] ?? '#ffffff' ),
		'--dcb-accept-hover-bg'     => sanitize_hex_color( $options['btn_accept_hover_bg'] ?? '#005177' ),
		'--dcb-accept-hover-text'   => sanitize_hex_color( $options['btn_accept_hover_text'] ?? '#ffffff' ),
		'--dcb-reject-bg'           => sanitize_hex_color( $options['btn_reject_bg'] ?? '#444444' ),
		'--dcb-reject-text'         => sanitize_hex_color( $options['btn_reject_text'] ?? '#ffffff' ),
		'--dcb-reject-hover-bg'     => sanitize_hex_color( $options['btn_reject_hover_bg'] ?? '#000000' ),
		'--dcb-reject-hover-text'   => sanitize_hex_color( $options['btn_reject_hover_text'] ?? '#ffffff' ),
	];

	$inline_vars = '';
	foreach ( $vars as $key => $value ) {
		if ( $value ) {
			$inline_vars .= $key . ':' . esc_attr( $value ) . ';';
		}
	}
	?>
	<div id="dcb-cookie-banner"
		class="dcb-position-<?php echo esc_attr( $position ); ?>"
		role="dialog"
		aria-live="polite"
		aria-label="<?php echo esc_attr__( 'Cookie consent banner', 'delight-cookie-banner' ); ?>"
		style="<?php echo esc_attr( $inline_vars ); ?>">

		<div class="dcb-inner">
			<p class="dcb-message">
				<?php echo esc_html( $text_msg ); ?>
				<a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo esc_html( $privacy_title ); ?>
				</a>
			</p>

			<div class="dcb-buttons">
				<button id="dcb-accept" class="dcb-btn">
					<?php echo esc_html( $text_accept ); ?>
				</button>

				<?php if ( $show_reject ) : ?>
					<button id="dcb-reject" class="dcb-btn dcb-reject">
						<?php echo esc_html( $text_reject ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}

	/**
	 * Output the “Change cookie settings” link in the global footer.
	 */
	public function render_footer_link() {
		$options = wp_parse_args( get_option( 'dcb_settings', [] ), ( new DCB_Admin() )->get_default_settings() );
		if ( empty( $options['show_footer_link'] ) ) {
			return;
		}

		printf(
			'<div id="dcb-footer-link" class="dcb-footer-center"><a href="javascript:void(0)" class="dcb-open" data-dcb-open>%s</a></div>',
			esc_html__( 'Change cookie settings', 'delight-cookie-banner' )
		);
	}

	// --- Shortcodes ---------------------------------------------------------

	public function shortcode_open( $atts = [], $content = null ) {
		$atts = shortcode_atts(
			[
				'label' => $content ?: esc_html__( 'Change cookie settings', 'delight-cookie-banner' ),
				'tag'   => 'a',
				'class' => '',
				'attr'  => '',
				'href'  => 'javascript:void(0)',
			],
			$atts,
			'dcb_open'
		);

		$tag        = in_array( strtolower( $atts['tag'] ), [ 'a', 'button' ], true ) ? strtolower( $atts['tag'] ) : 'a';
		$extra_attr = trim( wp_strip_all_tags( $atts['attr'] ) );
		$classes    = trim( 'dcb-open ' . sanitize_html_class( $atts['class'] ) );

		if ( 'button' === $tag ) {
			return sprintf(
				'<button type="button" class="%1$s" data-dcb-open %3$s>%2$s</button>',
				esc_attr( $classes ),
				esc_html( $atts['label'] ),
				$extra_attr
			);
		}

		return sprintf(
			'<a href="%4$s" class="%1$s" data-dcb-open %3$s>%2$s</a>',
			esc_attr( $classes ),
			esc_html( $atts['label'] ),
			$extra_attr,
			esc_url( $atts['href'] )
		);
	}

	public function shortcode_reset( $atts = [], $content = null ) {
		$atts = shortcode_atts(
			[
				'label' => $content ?: esc_html__( 'Reset cookie settings', 'delight-cookie-banner' ),
				'tag'   => 'a',
				'class' => '',
				'attr'  => '',
				'href'  => 'javascript:void(0)',
			],
			$atts,
			'dcb_reset'
		);

		$tag        = in_array( strtolower( $atts['tag'] ), [ 'a', 'button' ], true ) ? strtolower( $atts['tag'] ) : 'a';
		$extra_attr = trim( wp_strip_all_tags( $atts['attr'] ) );
		$classes    = trim( 'dcb-reset ' . sanitize_html_class( $atts['class'] ) );

		if ( 'button' === $tag ) {
			return sprintf(
				'<button type="button" class="%1$s" data-dcb-reset %3$s>%2$s</button>',
				esc_attr( $classes ),
				esc_html( $atts['label'] ),
				$extra_attr
			);
		}

		return sprintf(
			'<a href="%4$s" class="%1$s" data-dcb-reset %3$s>%2$s</a>',
			esc_attr( $classes ),
			esc_html( $atts['label'] ),
			$extra_attr,
			esc_url( $atts['href'] )
		);
	}
}
