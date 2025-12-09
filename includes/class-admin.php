<?php
/**
 * Handles the admin settings page for Delight Cookie Banner
 * Includes live preview and reset-to-defaults functionality.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PCDELICOBA_Admin {

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
		add_action( 'admin_post_pcdelicoba_reset_settings', [ $this, 'handle_reset_settings' ] );
	}

	public function get_default_settings(): array {
		return [
			'bg_color'              => '#ffffff',
			'text_color'            => '#000000',
			'btn_accept_bg'         => '#000000',
			'btn_accept_text'       => '#ffffff',
			'btn_accept_hover_bg'   => '#333333',
			'btn_accept_hover_text' => '#ffffff',
			'btn_reject_bg'         => '#ffffff',
			'btn_reject_text'       => '#000000',
			'btn_reject_hover_bg'   => '#eeeeee',
			'btn_reject_hover_text' => '#000000',
			'position'              => 'bottom',
			'text_message'          => esc_html__( 'We use cookies to improve your experience. By clicking “Accept”, you agree to the use of cookies as described in our Privacy Policy.', 'delight-cookie-banner' ),
			'text_accept'           => esc_html__( 'Accept', 'delight-cookie-banner' ),
			'text_reject'           => esc_html__( 'Reject', 'delight-cookie-banner' ),
			'privacy_page'          => 0,
			'show_footer_link'      => 1,
			'show_reject_button'    => 1, // default: show reject button
		];
	}

	public function enqueue_admin_assets( $hook ) {
		if ( 'settings_page_delight-cookie-banner' !== $hook ) {
			return;
		}

		wp_enqueue_style( 'pcdelicoba-admin-style', PCDELICOBA_URL . 'assets/css/admin.css', [], PCDELICOBA_VERSION );
		wp_enqueue_script( 'pcdelicoba-admin-script', PCDELICOBA_URL . 'assets/js/admin.js', [], PCDELICOBA_VERSION, true );

		wp_localize_script(
			'pcdelicoba-admin-script',
			'pcdelicobaAdminI18n',
			[
				'privacyPolicy'  => esc_html__( 'Privacy Policy', 'delight-cookie-banner' ),
				'changeSettings' => esc_html__( 'Change cookie settings', 'delight-cookie-banner' ),
			]
		);
	}

	public function add_admin_menu() {
		add_options_page(
			esc_html__( 'Delight Cookie Banner', 'delight-cookie-banner' ),
			esc_html__( 'Delight Cookie Banner', 'delight-cookie-banner' ),
			'manage_options',
			'delight-cookie-banner',
			[ $this, 'render_settings_page' ]
		);
	}

	public function register_settings() {
		register_setting(
			'pcdelicoba_settings_group',
			'pcdelicoba_settings',
			[
				'type'              => 'array',
				'sanitize_callback' => [ $this, 'sanitize_settings' ],
				'default'           => $this->get_default_settings(),
			]
		);

		add_settings_section(
			'pcdelicoba_main_section',
			esc_html__( 'Cookie Banner Settings', 'delight-cookie-banner' ),
			function() {
				echo '<p>' . esc_html__( 'Customize the appearance, text, and behavior of your cookie banner. The live preview updates instantly on the right.', 'delight-cookie-banner' ) . '</p>';
			},
			'delight-cookie-banner'
		);

		$fields = [
			'bg_color'              => esc_html__( 'Background Color', 'delight-cookie-banner' ),
			'text_color'            => esc_html__( 'Text Color', 'delight-cookie-banner' ),
			'btn_accept_bg'         => esc_html__( 'Accept Button Background', 'delight-cookie-banner' ),
			'btn_accept_text'       => esc_html__( 'Accept Button Text Color', 'delight-cookie-banner' ),
			'btn_accept_hover_bg'   => esc_html__( 'Accept Button Hover Background', 'delight-cookie-banner' ),
			'btn_accept_hover_text' => esc_html__( 'Accept Button Hover Text Color', 'delight-cookie-banner' ),
			'btn_reject_bg'         => esc_html__( 'Reject Button Background', 'delight-cookie-banner' ),
			'btn_reject_text'       => esc_html__( 'Reject Button Text Color', 'delight-cookie-banner' ),
			'btn_reject_hover_bg'   => esc_html__( 'Reject Button Hover Background', 'delight-cookie-banner' ),
			'btn_reject_hover_text' => esc_html__( 'Reject Button Hover Text Color', 'delight-cookie-banner' ),
			'position'              => esc_html__( 'Banner Position', 'delight-cookie-banner' ),
			'text_message'          => esc_html__( 'Banner Message', 'delight-cookie-banner' ),
			'text_accept'           => esc_html__( 'Accept Button Text', 'delight-cookie-banner' ),
			'text_reject'           => esc_html__( 'Reject Button Text', 'delight-cookie-banner' ),
			'privacy_page'          => esc_html__( 'Privacy Policy Page', 'delight-cookie-banner' ),
			'show_footer_link'      => esc_html__( 'Show Footer Link for Consent Change', 'delight-cookie-banner' ),
			'show_reject_button'    => esc_html__( 'Show “Reject” Button', 'delight-cookie-banner' ),
		];

		foreach ( $fields as $field => $label ) {
			add_settings_field(
				$field,
				esc_html( $label ),
				[ $this, 'render_field' ],
				'delight-cookie-banner',
				'pcdelicoba_main_section',
				[ 'id' => $field ]
			);
		}
	}

	public function sanitize_settings( $input ) {
		if ( ! is_array( $input ) ) {
			$input = [];
		}

		$defaults = $this->get_default_settings();

		// Ensure all checkboxes exist (set to 0 if missing)
		foreach ( [ 'show_footer_link', 'show_reject_button' ] as $checkbox ) {
			if ( ! isset( $input[ $checkbox ] ) ) {
				$input[ $checkbox ] = 0;
			}
		}

		$input = wp_parse_args( $input, $defaults );
		$output = [];

		foreach ( $input as $key => $value ) {
			switch ( $key ) {
				case 'bg_color':
				case 'text_color':
				case 'btn_accept_bg':
				case 'btn_accept_text':
				case 'btn_accept_hover_bg':
				case 'btn_accept_hover_text':
				case 'btn_reject_bg':
				case 'btn_reject_text':
				case 'btn_reject_hover_bg':
				case 'btn_reject_hover_text':
					$output[ $key ] = sanitize_hex_color( $value ) ?: $defaults[ $key ];
					break;
				case 'position':
					$output[ $key ] = in_array( $value, [ 'top', 'bottom' ], true ) ? $value : 'bottom';
					break;
				case 'text_message':
					$output[ $key ] = sanitize_textarea_field( $value );
					break;
				case 'text_accept':
				case 'text_reject':
					$output[ $key ] = sanitize_text_field( $value );
					break;
				case 'privacy_page':
					$output[ $key ] = absint( $value );
					break;
				case 'show_footer_link':
				case 'show_reject_button':
					$output[ $key ] = (int) ! empty( $value );
					break;
				default:
					$output[ $key ] = sanitize_text_field( $value );
					break;
			}
		}

		return $output;
	}
	public function render_field( $args ) {
		$stored  = get_option( 'pcdelicoba_settings' );
		$options = is_array( $stored ) ? array_merge( $this->get_default_settings(), $stored ) : $this->get_default_settings();

		$id    = $args['id'];
		$value = $options[ $id ];

		switch ( $id ) {
			case 'bg_color':
			case 'text_color':
			case 'btn_accept_bg':
			case 'btn_accept_text':
			case 'btn_accept_hover_bg':
			case 'btn_accept_hover_text':
			case 'btn_reject_bg':
			case 'btn_reject_text':
			case 'btn_reject_hover_bg':
			case 'btn_reject_hover_text':
				printf(
					'<input type="color" class="pcdelicoba-bind" data-bind="%1$s" name="pcdelicoba_settings[%1$s]" value="%2$s">',
					esc_attr( $id ),
					esc_attr( $value )
				);
				break;

			case 'position':
				$positions = [
					'top'    => __( 'Top', 'delight-cookie-banner' ),
					'bottom' => __( 'Bottom', 'delight-cookie-banner' ),
				];
				echo '<select class="pcdelicoba-bind" data-bind="position" name="pcdelicoba_settings[' . esc_attr( $id ) . ']">';
				foreach ( $positions as $val => $label ) {
					printf(
						'<option value="%s" %s>%s</option>',
						esc_attr( $val ),
						selected( $value, $val, false ),
						esc_html( $label )
					);
				}
				echo '</select>';
				break;

			case 'text_message':
				$saved_or_default = isset( $options['text_message'] ) ? trim( $options['text_message'] ) : '';
				echo '<textarea class="large-text pcdelicoba-bind" data-bind="text_message" name="pcdelicoba_settings[text_message]" rows="3">'
					. esc_textarea( $saved_or_default ) .
					'</textarea>';
				echo '<p class="description">';
				echo esc_html__( 'This field applies only to the default language. Other translations must be changed using a translation tool (e.g. Loco Translate or Polylang).', 'delight-cookie-banner' );
				echo '</p>';
				break;

			case 'text_accept':
			case 'text_reject':
				printf(
					'<input type="text" class="regular-text pcdelicoba-bind" data-bind="%1$s" name="pcdelicoba_settings[%1$s]" value="%2$s">',
					esc_attr( $id ),
					esc_attr( $value )
				);
				break;

			case 'privacy_page':
				$preselect = $value ?: get_option( 'wp_page_for_privacy_policy' );
				wp_dropdown_pages( [
					'name'              => 'pcdelicoba_settings[' . esc_attr( $id ) . ']',
					'show_option_none'  => esc_html__( 'Select a page', 'delight-cookie-banner' ),
					'option_none_value' => '',
					'selected'          => absint( $preselect ),
					'class'             => 'pcdelicoba-bind',
				] );
				break;

			case 'show_footer_link':
			case 'show_reject_button':
				$desc = ( 'show_footer_link' === $id )
					? esc_html__( 'Automatically display a “Change cookie settings” link in the footer. You can also insert the same link anywhere on your site using the [pcdelicoba_open] shortcode, or add a reset link with [pcdelicoba_reset].', 'delight-cookie-banner' )
					: esc_html__( 'Display the “Reject” button in the banner (visual option only).', 'delight-cookie-banner' );

				// Hidden input ensures “0” is submitted when unchecked.
				printf(
					'<input type="hidden" name="pcdelicoba_settings[%1$s]" value="0">
					<label><input type="checkbox" class="pcdelicoba-bind" data-bind="%1$s" name="pcdelicoba_settings[%1$s]" value="1" %2$s> %3$s</label>',
					esc_attr( $id ),
					checked( ! empty( $value ), true, false ),
					esc_html( $desc )
				);
				break;

			default:
				printf(
					'<input type="text" class="regular-text pcdelicoba-bind" data-bind="%1$s" name="pcdelicoba_settings[%1$s]" value="%2$s">',
					esc_attr( $id ),
					esc_attr( $value )
				);
				break;
		}
	}

	public function handle_reset_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to perform this action.', 'delight-cookie-banner' ) );
		}
		check_admin_referer( 'pcdelicoba_reset_settings' );

		update_option( 'pcdelicoba_settings', $this->get_default_settings() );

		wp_safe_redirect(
			add_query_arg(
				[ 'page' => 'delight-cookie-banner', 'pcdelicoba_reset' => '1' ],
				admin_url( 'options-general.php' )
			)
		);
		exit;
	}

	public function render_settings_page() {
		$stored  = get_option( 'pcdelicoba_settings' );
		$options = is_array( $stored ) ? array_merge( $this->get_default_settings(), $stored ) : $this->get_default_settings();

		$bg_color              = $options['bg_color'];
		$text_color            = $options['text_color'];
		$btn_accept_bg         = $options['btn_accept_bg'];
		$btn_accept_text       = $options['btn_accept_text'];
		$btn_accept_hover_bg   = $options['btn_accept_hover_bg'];
		$btn_accept_hover_text = $options['btn_accept_hover_text'];
		$btn_reject_bg         = $options['btn_reject_bg'];
		$btn_reject_text       = $options['btn_reject_text'];
		$btn_reject_hover_bg   = $options['btn_reject_hover_bg'];
		$btn_reject_hover_text = $options['btn_reject_hover_text'];
		$position              = $options['position'];
		$text_message          = $options['text_message'];
		$text_accept           = $options['text_accept'];
		$text_reject           = $options['text_reject'];
		$show_reject_button    = ! empty( $options['show_reject_button'] );
		$privacy_id            = $options['privacy_page'] ?: get_option( 'wp_page_for_privacy_policy' );
		$privacy_url           = $privacy_id ? get_permalink( $privacy_id ) : '#';

		// We only read a GET flag for displaying a notice, no action is performed.
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$pcdelicoba_reset = isset( $_GET['pcdelicoba_reset'] )
			? sanitize_text_field( wp_unslash( $_GET['pcdelicoba_reset'] ) )
			: '';

		if ( $pcdelicoba_reset === '1' ) {
			echo '<div class="notice notice-success is-dismissible"><p>' .
				  esc_html__( 'Settings have been reset to defaults.', 'delight-cookie-banner' ) .
				 '</p></div>';
		}
		// phpcs:enable WordPress.Security.NonceVerification.Recommended
		
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'Delight Cookie Banner', 'delight-cookie-banner' ); ?></h1>

			<div class="pcdelicoba-grid">
				<div class="pcdelicoba-grid-left">
					<form action="options.php" method="post" style="margin-bottom:16px;">
						<?php
						settings_fields( 'pcdelicoba_settings_group' );
						do_settings_sections( 'delight-cookie-banner' );
						submit_button( esc_html__( 'Save Settings', 'delight-cookie-banner' ) );
						?>
					</form>

					<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" onsubmit="return confirm('<?php echo esc_attr__( 'Reset all settings to their installation defaults?', 'delight-cookie-banner' ); ?>');">
						<input type="hidden" name="action" value="pcdelicoba_reset_settings">
						<?php wp_nonce_field( 'pcdelicoba_reset_settings' ); ?>
						<?php submit_button( esc_html__( 'Reset to defaults', 'delight-cookie-banner' ), 'secondary' ); ?>
					</form>
				</div>

				<div class="pcdelicoba-grid-right">
					<h2 class="pcdelicoba-preview-title"><?php echo esc_html__( 'Live Preview', 'delight-cookie-banner' ); ?></h2>

					<?php
					$privacy_title = $privacy_id
						? get_the_title( $privacy_id )
						: esc_html__( 'Privacy Policy', 'delight-cookie-banner' );
					?>

					<div id="pcdelicoba-preview"
						class="pcdelicoba-preview pcdelicoba-position-<?php echo esc_attr( $position ); ?>"
						style="
							--pcdelicoba-bg-color: <?php echo esc_attr( $bg_color ); ?>;
							--pcdelicoba-text-color: <?php echo esc_attr( $text_color ); ?>;
							--pcdelicoba-accept-bg: <?php echo esc_attr( $btn_accept_bg ); ?>;
							--pcdelicoba-accept-text: <?php echo esc_attr( $btn_accept_text ); ?>;
							--pcdelicoba-accept-hover-bg: <?php echo esc_attr( $btn_accept_hover_bg ); ?>;
							--pcdelicoba-accept-hover-text: <?php echo esc_attr( $btn_accept_hover_text ); ?>;
							--pcdelicoba-reject-bg: <?php echo esc_attr( $btn_reject_bg ); ?>;
							--pcdelicoba-reject-text: <?php echo esc_attr( $btn_reject_text ); ?>;
							--pcdelicoba-reject-hover-bg: <?php echo esc_attr( $btn_reject_hover_bg ); ?>;
							--pcdelicoba-reject-hover-text: <?php echo esc_attr( $btn_reject_hover_text ); ?>;
						"
					>
						<div class="pcdelicoba-banner">
							<p class="pcdelicoba-message">
								<span class="js-pcdelicoba-message"><?php echo esc_html( $text_message ); ?></span>
								<a class="js-pcdelicoba-privacy" href="<?php echo esc_url( $privacy_url ); ?>" target="_blank" rel="noopener noreferrer">
									<?php echo esc_html( $privacy_title ); ?>
								</a>
							</p>
							<div class="pcdelicoba-buttons">
								<button type="button" class="pcdelicoba-btn pcdelicoba-accept js-pcdelicoba-accept">
									<?php echo esc_html( $text_accept ); ?>
								</button>
								<?php if ( $show_reject_button ) : ?>
									<button type="button" class="pcdelicoba-btn pcdelicoba-reject js-pcdelicoba-reject">
										<?php echo esc_html( $text_reject ); ?>
									</button>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<p class="description">
						<?php echo esc_html__( 'This preview reflects your current settings. Hover the buttons to see hover colors. Save to persist changes.', 'delight-cookie-banner' ); ?>
					</p>
				</div>
			</div>
		</div>
		<?php
	}
}
