<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {
	
	public function init() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_settings() {
		// Group: bytesis_donation_settings
		$settings = array(
			'bytesis_donation_preset_amounts',
			'bytesis_donation_smtp_host',
			'bytesis_donation_smtp_port',
			'bytesis_donation_smtp_user',
			'bytesis_donation_smtp_pass',
			'bytesis_donation_smtp_from_name',
			'bytesis_donation_smtp_from_email',
			'bytesis_donation_email_enabled',
			'bytesis_donation_email_subject',
			'bytesis_donation_email_body',
			'bytesis_donation_funds' // Stores array of fund categories
		);

		foreach ( $settings as $setting ) {
			register_setting( 'bytesis_donation_settings', $setting );
		}
	}

	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Save handler for custom fund/amount actions could go here
		$this->handle_form_actions();

		include BYTESIS_DONATION_PLUGIN_DIR . 'includes/admin/views/settings-page.php';
	}

	private function handle_form_actions() {
		// Simple action handler for adding/removing funds & amounts
		if ( isset( $_POST['bytesis_action'] ) && check_admin_referer( 'bytesis_settings_action' ) ) {
			if ( $_POST['bytesis_action'] === 'add_fund' && !empty($_POST['fund_name']) ) {
				$fund_manager = new Fund_Manager();
				$fund_manager->add_fund( sanitize_text_field( $_POST['fund_name'] ) );
			}
			
			if ( $_POST['bytesis_action'] === 'delete_fund' && isset($_POST['fund_id']) ) {
				$fund_manager = new Fund_Manager();
				$fund_manager->delete_fund( sanitize_text_field( $_POST['fund_id'] ) );
			}
		}
	}
}
