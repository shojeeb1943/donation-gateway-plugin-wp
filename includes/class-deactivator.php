<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Deactivator {
	public static function deactivate() {
		// Nothing to do for now, we don't drop the table on deactivation to preserve data
	}
}
