<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WooCommerce_Integration {
	
	public function init() {
		add_action( 'admin_post_bytesis_process_donation', array( $this, 'process_donation_form' ) );
		add_action( 'admin_post_nopriv_bytesis_process_donation', array( $this, 'process_donation_form' ) );
		
		// Handle WooCommerce Order Status changes to record successful donations
		add_action( 'woocommerce_order_status_completed', array( $this, 'handle_order_completed' ), 10, 2 );
		add_action( 'woocommerce_order_status_processing', array( $this, 'handle_order_completed' ), 10, 2 );
	}

	public function process_donation_form() {
		if ( ! isset( $_POST['bytesis_nonce'] ) || ! wp_verify_nonce( $_POST['bytesis_nonce'], 'bytesis_donation_nonce' ) ) {
			wp_die( 'Security check failed.' );
		}

		if ( ! class_exists( 'WooCommerce' ) ) {
			wp_die( 'WooCommerce is not active.' );
		}

		$donor_name  = sanitize_text_field( $_POST['donor_name'] );
		$donor_email = sanitize_email( $_POST['donor_email'] );
		$donor_phone = sanitize_text_field( $_POST['donor_phone'] );
		$fund_id     = sanitize_text_field( $_POST['fund_id'] );
		$amount      = floatval( $_POST['amount'] );

		if ( empty( $donor_name ) || empty( $donor_email ) || empty( $fund_id ) || $amount <= 0 ) {
			wp_die( 'Invalid input.' );
		}

		// Get Fund details
		$fund_manager = new Fund_Manager();
		$funds = $fund_manager->get_funds();
		
		if ( ! isset( $funds[$fund_id] ) ) {
			wp_die( 'Invalid fund selected.' );
		}
		
		$fund = $funds[$fund_id];
		$wc_product_id = $fund['wc_product_id'];

		$product = wc_get_product( $wc_product_id );
		if ( ! $product ) {
			wp_die( 'Fund product not found.' );
		}

		// Create WooCommerce Order programmatically
		$order = wc_create_order();
		
		// Add product with custom amount
		$order->add_product( $product, 1, array(
			'subtotal' => $amount,
			'total'    => $amount,
		) );

		// Set billing address
		$name_parts = explode( ' ', $donor_name, 2 );
		$first_name = $name_parts[0];
		$last_name  = isset( $name_parts[1] ) ? $name_parts[1] : '';

		$address = array(
			'first_name' => $first_name,
			'last_name'  => $last_name,
			'email'      => $donor_email,
			'phone'      => $donor_phone,
		);
		$order->set_address( $address, 'billing' );
		
		$order->calculate_totals();

		// Set payment method to SSLCommerz (assuming standard gateway id for the plugin is 'sslcommerz')
		$order->set_payment_method( 'sslcommerz' );
		
		// Save custom meta to identify it as a Bytesis donation order
		$order->update_meta_data( '_is_bytesis_donation', 'yes' );
		$order->update_meta_data( '_bytesis_fund_id', $fund_id );
		$order->update_meta_data( '_bytesis_fund_name', $fund['name'] );
		$order->save();

		// Save initial pending record in our custom table
		global $wpdb;
		$table_name = $wpdb->prefix . 'bytesis_donations';
		$wpdb->insert(
			$table_name,
			array(
				'donor_name'  => $donor_name,
				'donor_email' => $donor_email,
				'donor_phone' => $donor_phone,
				'fund_id'     => $fund['id'],
				'fund_name'   => $fund['name'],
				'amount'      => $amount,
				'wc_order_id' => $order->get_id(),
				'status'      => 'pending',
				'created_at'  => current_time( 'mysql' )
			)
		);

		// Redirect to WooCommerce payment page — SSLCommerz handles the rest
		wp_redirect( $order->get_checkout_payment_url( true ) );
		exit;
	}

	public function handle_order_completed( $order_id, $order ) {
		// Check if it's a donation order
		if ( $order->get_meta( '_is_bytesis_donation' ) !== 'yes' ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'bytesis_donations';
		
		// Update our custom table status to completed
		$wpdb->update(
			$table_name,
			array( 
				'status' => 'completed',
				'transaction_id' => $order->get_transaction_id()
			),
			array( 'wc_order_id' => $order_id )
		);
	}
}
