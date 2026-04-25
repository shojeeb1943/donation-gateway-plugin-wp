<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Donations_Page {
	public function init() {
		add_action( 'admin_init', array( $this, 'handle_export' ) );
	}

	public function render_page() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'bytesis_donations';

		$search = isset($_GET['s']) ? sanitize_text_field($_GET['s']) : '';
		
		$query = "SELECT * FROM $table_name";
		if ( !empty($search) ) {
			$like = '%' . $wpdb->esc_like( $search ) . '%';
			$query .= $wpdb->prepare( " WHERE donor_name LIKE %s OR donor_email LIKE %s OR transaction_id LIKE %s", $like, $like, $like );
		}
		$query .= " ORDER BY created_at DESC";

		$results = $wpdb->get_results( $query );

		include BYTESIS_DONATION_PLUGIN_DIR . 'includes/admin/views/donations-page.php';
	}

	public function handle_export() {
		if ( isset($_GET['bytesis_export_donations']) && current_user_can('manage_options') ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'bytesis_donations';
			$results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY created_at DESC", ARRAY_A );

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=bytesis-donations-' . date('Y-m-d') . '.csv');
			$output = fopen('php://output', 'w');
			fputcsv($output, array('ID', 'Donor Name', 'Donor Email', 'Phone', 'Fund Name', 'Amount', 'Status', 'Transaction ID', 'Date'));

			if ( $results ) {
				foreach ( $results as $row ) {
					fputcsv($output, array(
						$row['id'],
						$row['donor_name'],
						$row['donor_email'],
						$row['donor_phone'],
						$row['fund_name'],
						$row['amount'],
						$row['status'],
						$row['transaction_id'],
						$row['created_at']
					));
				}
			}
			fclose($output);
			exit;
		}
	}
}
