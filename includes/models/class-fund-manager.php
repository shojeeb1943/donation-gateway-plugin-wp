<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Fund_Manager {

	public function get_funds() {
		$funds = get_option( 'bytesis_donation_funds', array() );
		return is_array( $funds ) ? $funds : array();
	}

	public function add_fund( $name ) {
		$funds = $this->get_funds();
		$fund_id = uniqid('fund_');
		
		// Create associated WooCommerce Product
		$wc_product_id = $this->create_wc_product( $name );

		$funds[$fund_id] = array(
			'id' => $fund_id,
			'name' => $name,
			'wc_product_id' => $wc_product_id,
			'is_active' => true,
			'created_at' => current_time( 'mysql' )
		);

		update_option( 'bytesis_donation_funds', $funds );
		return $fund_id;
	}

	public function delete_fund( $fund_id ) {
		$funds = $this->get_funds();
		if ( isset( $funds[$fund_id] ) ) {
			// Optional: Trash the WC product
			$wc_product_id = $funds[$fund_id]['wc_product_id'];
			if ( $wc_product_id ) {
				wp_trash_post( $wc_product_id );
			}

			unset( $funds[$fund_id] );
			update_option( 'bytesis_donation_funds', $funds );
		}
	}

	private function create_wc_product( $name ) {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return 0;
		}

		$product = new \WC_Product_Simple();
		$product->set_name( $name . ' (Donation Fund)' );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'hidden' ); // Hide from shop page
		$product->set_price( '1' ); // Base price, will be overridden
		$product->set_regular_price( '1' );
		$product->set_virtual( true ); // No shipping
		$product->set_sold_individually( true );
		
		return $product->save();
	}
}
