<?php
/**
 * Plugin Name: Bytesis Donation Gateway
 * Plugin URI: https://bytesis.com
 * Description: Custom WordPress plugin that enables organizations to accept online donations via SSLCommerz and WooCommerce.
 * Version: 1.0.0
 * Author: Bytesis Ltd.
 * Author URI: https://bytesis.com
 * License: GPLv2 or later
 * Text Domain: bytesis-donation
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Define plugin constants
define( 'BYTESIS_DONATION_VERSION', '1.0.0' );
define( 'BYTESIS_DONATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BYTESIS_DONATION_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include necessary files
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/class-activator.php';
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/class-deactivator.php';
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/models/class-fund-manager.php';
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/admin/class-donations-page.php';
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/admin/class-settings.php';
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/admin/class-admin-menu.php';
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/frontend/class-shortcode.php';
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/integration/class-woocommerce.php';
require_once BYTESIS_DONATION_PLUGIN_DIR . 'includes/emails/class-mailer.php';

// Register Activation & Deactivation hooks
register_activation_hook( __FILE__, array( 'Bytesis\\DonationGateway\\Activator', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Bytesis\\DonationGateway\\Deactivator', 'deactivate' ) );

/**
 * Check dependencies and initialize the plugin
 */
function bytesis_donation_init() {
	// Check for WooCommerce
	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'bytesis_donation_missing_wc_notice' );
		return;
	}

	// Initialize Admin Menu and Settings
	if ( is_admin() ) {
		$settings = new \Bytesis\DonationGateway\Settings();
		$settings->init();

		$donations_page = new \Bytesis\DonationGateway\Donations_Page();
		$donations_page->init();

		$admin_menu = new \Bytesis\DonationGateway\Admin_Menu();
		$admin_menu->init();
	}

	// Initialize Shortcode
	$shortcode = new \Bytesis\DonationGateway\Shortcode();
	$shortcode->init();

	// Initialize WooCommerce Integration
	$wc_integration = new \Bytesis\DonationGateway\WooCommerce_Integration();
	$wc_integration->init();

	// Initialize Mailer
	$mailer = new \Bytesis\DonationGateway\Mailer();
	$mailer->init();
}
add_action( 'plugins_loaded', 'bytesis_donation_init' );

function bytesis_donation_missing_wc_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php esc_html_e( 'Bytesis Donation Gateway requires WooCommerce to be installed and active.', 'bytesis-donation' ); ?></p>
	</div>
	<?php
}
