<?php
/**
 * Plugin Name: Delight Cookie Banner
 * Plugin URI: https://pcdelight.ch/wordpress-delight-cookie-banner-free/
 * Description: A minimal and multilingual cookie notice for WordPress. GDPR-friendly, lightweight, and compatible with all themes and WooCommerce. Includes live preview and user-friendly language display.
 * Version: 1.2.0
 * Author: Leandro Ciuffi
 * Author URI: https://pcdelight.ch
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: delight-cookie-banner
 * Domain Path: /languages
 *
 * @package DelightCookieBanner
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ----------------------------------------------------
// 1. Define plugin constants
// ----------------------------------------------------
define( 'PCDELICOBA_PATH', plugin_dir_path( __FILE__ ) );
define( 'PCDELICOBA_URL', plugin_dir_url( __FILE__ ) );
define( 'PCDELICOBA_VERSION', '1.2.0' );

// ----------------------------------------------------
// 2. Include required class files
// ----------------------------------------------------
require_once PCDELICOBA_PATH . 'includes/class-assets.php';
require_once PCDELICOBA_PATH . 'includes/class-admin.php';
require_once PCDELICOBA_PATH . 'includes/class-frontend.php';

// ----------------------------------------------------
// 3. Main plugin bootstrap
// ----------------------------------------------------
class PCDELICOBA_Cookie_Banner {

	/**
	 * Constructor - hook initialization.
	 */
	public function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	/**
	 * Initialize plugin features.
	 */
	public function init() {

		// Initialize main components.
		new PCDELICOBA_Assets();
		new PCDELICOBA_Admin();
		new PCDELICOBA_Frontend();
	}
}

// ----------------------------------------------------
// 4. Start the plugin
// ----------------------------------------------------
new PCDELICOBA_Cookie_Banner();
