<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Admin_Menu {
	public function init() {
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	public function add_plugin_admin_menu() {
		// Main menu — points to Overview
		add_menu_page(
			__( 'Bytesis Donation', 'bytesis-donation' ),
			__( 'Bytesis Donation', 'bytesis-donation' ),
			'manage_options',
			'bytesis-donation',
			array( $this, 'display_overview_page' ),
			'dashicons-heart',
			56
		);

		// Submenu: Overview (replaces the duplicate main menu entry)
		add_submenu_page(
			'bytesis-donation',
			__( 'Overview', 'bytesis-donation' ),
			__( 'Overview', 'bytesis-donation' ),
			'manage_options',
			'bytesis-donation',
			array( $this, 'display_overview_page' )
		);

		// Submenu: Donations
		add_submenu_page(
			'bytesis-donation',
			__( 'Donations', 'bytesis-donation' ),
			__( 'Donations', 'bytesis-donation' ),
			'manage_options',
			'bytesis-donation-records',
			array( $this, 'display_donations_page' )
		);

		// Submenu: Settings
		add_submenu_page(
			'bytesis-donation',
			__( 'Settings', 'bytesis-donation' ),
			__( 'Settings', 'bytesis-donation' ),
			'manage_options',
			'bytesis-donation-settings',
			array( $this, 'display_settings_page' )
		);
	}

	public function enqueue_admin_styles( $hook ) {
		// Only load our styles on our plugin pages
		if ( strpos( $hook, 'bytesis-donation' ) !== false ) {
			wp_enqueue_style(
				'bytesis-donation-admin-css',
				BYTESIS_DONATION_PLUGIN_URL . 'assets/admin/css/bytesis-admin.css',
				array(),
				BYTESIS_DONATION_VERSION,
				'all'
			);
		}
	}

	public function display_overview_page() {
		include BYTESIS_DONATION_PLUGIN_DIR . 'includes/admin/views/overview-page.php';
	}

	public function display_donations_page() {
		$donations_page = new \Bytesis\DonationGateway\Donations_Page();
		$donations_page->render_page();
	}

	public function display_settings_page() {
		$settings = new \Bytesis\DonationGateway\Settings();
		$settings->render_settings_page();
	}
}
