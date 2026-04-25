<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Activator {
	public static function activate() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'bytesis_donations';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			donor_name varchar(255) NOT NULL,
			donor_email varchar(255) NOT NULL,
			donor_phone varchar(20) NOT NULL,
			fund_id mediumint(9) NOT NULL,
			fund_name varchar(255) NOT NULL,
			amount decimal(10,2) NOT NULL,
			wc_order_id mediumint(9) NOT NULL,
			transaction_id varchar(100) DEFAULT '' NOT NULL,
			status varchar(50) DEFAULT 'pending' NOT NULL,
			created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
			PRIMARY KEY  (id)
		) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}
}
