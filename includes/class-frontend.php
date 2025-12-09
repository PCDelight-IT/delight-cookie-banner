<?php
/**
 * Handles the frontend rendering for Delight Cookie Banner.
 * Always renders banner (JS controls visibility).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PCDELICOBA_Frontend {

	public function __construct() {
		add_action( 'wp_footer', [ $this, 'render_cookie_banner' ] );
		add_action( 'wp_footer', [ $this, 'render_footer_link' ], 999 );
		add_shortcode( 'pcdelicoba_open', [ $this, 'shortcode_open' ] );
		add_shortcode( 'pcdelicoba_reset', [ $this, 'shortcode_reset' ] );
	}

	/**
	 * Render the cookie banner markup.
	 */
public function render_cookie_banner() {

	$defaults = ( new PCDELICOBA_Admin() )->get_default_settings();
	$options  = wp_parse_args( get_option( 'pcdelicoba_settings', [] ), $defaults );

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
		'--pcdelicoba-bg-color'            => sanitize_hex_color( $options['bg_color'] ?? '#ffffff' ),
		'--pcdelicoba-text-color'          => sanitize_hex_color( $options['text_color'] ?? '#000000' ),
		'--pcdelicoba-accept-bg'           => sanitize_hex_color( $options['btn_accept_bg'] ?? '#0073aa' ),
		'--pcdelicoba-accept-text'         => sanitize_hex_color( $options['btn_accept_text'] ?? '#ffffff' ),
		'--pcdelicoba-accept-hover-bg'     => sanitize_hex_color( $options['btn_accept_hover_bg'] ?? '#005177' ),
		'--pcdelicoba-accept-hover-text'   => sanitize_hex_color( $options['btn_accept_hover_text'] ?? '#ffffff' ),
		'--pcdelicoba-reject-bg'           => sanitize_hex_color( $options['btn_reject_bg'] ?? '#444444' ),
		'--pcdelicoba-reject-text'         => sanitize_hex_color( $options['btn_reject_text'] ?? '#ffffff' ),
		'--pcdelicoba-reject-hover-bg'     => sanitize_hex_color( $options['btn_reject_hover_bg'] ?? '#000000' ),
		'--pcdelicoba-reject-hover-text'   => sanitize_hex_color( $options['btn_reject_hover_text'] ?? '#ffffff' ),
	];

	$inline_vars = '';
	foreach ( $vars as $key => $value ) {
		if ( $value ) {
			$inline_vars .= $key . ':' . esc_attr( $value ) . ';';
		}
	}
	?>
	<div id="pcdelicoba-cookie-banner"
		class="pcdelicoba-position-<?php echo esc_attr( $position ); ?>"
		role="dialog"
		aria-live="polite"
		aria-label="<?php echo esc_attr__( 'Cookie consent banner', 'delight-cookie-banner' ); ?>"
		style="<?php echo esc_attr( $inline_vars ); ?>">

		<div class="pcdelicoba-inner">
			<p class="pcdelicoba-message">
				<?php echo esc_html( $text_msg ); ?>
				<a href="<?php echo esc_url( $privacy_url ); ?>" target="_blank" rel="noopener noreferrer">
					<?php echo esc_html( $privacy_title ); ?>
				</a>
			</p>

			<div class="pcdelicoba-buttons">
				<button id="pcdelicoba-accept" class="pcdelicoba-btn">
					<?php echo esc_html( $text_accept ); ?>
				</button>

				<?php if ( $show_reject ) : ?>
					<button id="pcdelicoba-reject" class="pcdelicoba-btn pcdelicoba-reject">
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
		$options = wp_parse_args( get_option( 'pcdelicoba_settings', [] ), ( new PCDELICOBA_Admin() )->get_default_settings() );
		if ( empty( $options['show_footer_link'] ) ) {
			return;
		}

		printf(
			'<div id="pcdelicoba-footer-link" class="pcdelicoba-footer-center"><a href="javascript:void(0)" class="pcdelicoba-open" data-pcdelicoba-open>%s</a></div>',
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
			'pcdelicoba_open'
		);

		$tag        = in_array( strtolower( $atts['tag'] ), [ 'a', 'button' ], true ) ? strtolower( $atts['tag'] ) : 'a';
		$extra_attr = trim( wp_strip_all_tags( $atts['attr'] ) );
		$classes    = trim( 'pcdelicoba-open ' . sanitize_html_class( $atts['class'] ) );

		if ( 'button' === $tag ) {
			return sprintf(
				'<button type="button" class="%1$s" data-pcdelicoba-open %3$s>%2$s</button>',
				esc_attr( $classes ),
				esc_html( $atts['label'] ),
				$extra_attr
			);
		}

		return sprintf(
			'<a href="%4$s" class="%1$s" data-pcdelicoba-open %3$s>%2$s</a>',
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
			'pcdelicoba_reset'
		);

		$tag        = in_array( strtolower( $atts['tag'] ), [ 'a', 'button' ], true ) ? strtolower( $atts['tag'] ) : 'a';
		$extra_attr = trim( wp_strip_all_tags( $atts['attr'] ) );
		$classes    = trim( 'pcdelicoba-reset ' . sanitize_html_class( $atts['class'] ) );

		if ( 'button' === $tag ) {
			return sprintf(
				'<button type="button" class="%1$s" data-pcdelicoba-reset %3$s>%2$s</button>',
				esc_attr( $classes ),
				esc_html( $atts['label'] ),
				$extra_attr
			);
		}

		return sprintf(
			'<a href="%4$s" class="%1$s" data-pcdelicoba-reset %3$s>%2$s</a>',
			esc_attr( $classes ),
			esc_html( $atts['label'] ),
			$extra_attr,
			esc_url( $atts['href'] )
		);
	}
}
