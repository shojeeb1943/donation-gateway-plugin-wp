<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shortcode {
	public function init() {
		add_shortcode( 'bytesis_donation_form', array( $this, 'render_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		// Register but don't enqueue globally. Only enqueue when shortcode is rendered.
		wp_register_style(
			'bytesis-donation-frontend-css',
			BYTESIS_DONATION_PLUGIN_URL . 'assets/css/frontend.css',
			array(),
			BYTESIS_DONATION_VERSION
		);

		wp_register_script(
			'bytesis-donation-frontend-js',
			BYTESIS_DONATION_PLUGIN_URL . 'assets/js/frontend-form.js',
			array( 'jquery' ),
			BYTESIS_DONATION_VERSION,
			true
		);
		
		wp_localize_script( 'bytesis-donation-frontend-js', 'bytesis_ajax', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'bytesis_donation_nonce' )
		));
	}

	public function render_shortcode( $atts ) {
		wp_enqueue_style( 'bytesis-donation-frontend-css' );
		wp_enqueue_script( 'bytesis-donation-frontend-js' );

		ob_start();
		include BYTESIS_DONATION_PLUGIN_DIR . 'includes/frontend/views/form-template.php';
		return ob_get_clean();
	}
}
