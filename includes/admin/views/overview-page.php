<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fund_manager = new \Bytesis\DonationGateway\Fund_Manager();
$funds = $fund_manager->get_funds();
$fund_count = count( $funds );

global $wpdb;
$table_name = $wpdb->prefix . 'bytesis_donations';
$total_donations = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
$total_completed = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name WHERE status = 'completed'" );
$total_revenue = $wpdb->get_var( "SELECT COALESCE(SUM(amount), 0) FROM $table_name WHERE status = 'completed'" );
?>
<div class="wrap bytesis-admin-wrap">

	<!-- Header -->
	<div style="margin-bottom: 32px;">
		<h1 style="font-size: 32px; font-weight: 540; letter-spacing: -0.8px; margin-bottom: 4px;">Bytesis Donation Gateway</h1>
		<p style="font-weight: 330; color: #666; margin: 0;">Accept donations on your WordPress site via SSLCommerz & WooCommerce.</p>
	</div>

	<!-- Quick Stats -->
	<div style="display: flex; gap: 16px; margin-bottom: 32px; flex-wrap: wrap;">
		<div style="flex: 1; min-width: 140px; background: #000; color: #fff; padding: 20px 24px; border-radius: 8px;">
			<span style="display: block; font-family: 'bytesisMono', monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; opacity: 0.6; margin-bottom: 4px;">Total Donations</span>
			<span style="font-size: 28px; font-weight: 540; letter-spacing: -0.5px;"><?php echo esc_html( $total_donations ); ?></span>
		</div>
		<div style="flex: 1; min-width: 140px; background: #000; color: #fff; padding: 20px 24px; border-radius: 8px;">
			<span style="display: block; font-family: 'bytesisMono', monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; opacity: 0.6; margin-bottom: 4px;">Completed</span>
			<span style="font-size: 28px; font-weight: 540; letter-spacing: -0.5px;"><?php echo esc_html( $total_completed ); ?></span>
		</div>
		<div style="flex: 1; min-width: 140px; background: #000; color: #fff; padding: 20px 24px; border-radius: 8px;">
			<span style="display: block; font-family: 'bytesisMono', monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; opacity: 0.6; margin-bottom: 4px;">Revenue</span>
			<span style="font-size: 28px; font-weight: 540; letter-spacing: -0.5px;">৳<?php echo esc_html( number_format( $total_revenue, 0 ) ); ?></span>
		</div>
		<div style="flex: 1; min-width: 140px; background: #000; color: #fff; padding: 20px 24px; border-radius: 8px;">
			<span style="display: block; font-family: 'bytesisMono', monospace; font-size: 11px; text-transform: uppercase; letter-spacing: 0.6px; opacity: 0.6; margin-bottom: 4px;">Active Funds</span>
			<span style="font-size: 28px; font-weight: 540; letter-spacing: -0.5px;"><?php echo esc_html( $fund_count ); ?></span>
		</div>
	</div>

	<!-- Shortcode Guide -->
	<div style="background: #f9f9f9; border: 1px solid #e8e8e8; border-radius: 8px; padding: 28px 32px; margin-bottom: 24px;">
		<h2 style="font-size: 18px; font-weight: 540; letter-spacing: -0.3px; margin: 0 0 12px 0;">How to Display the Donation Form</h2>
		<p style="font-weight: 330; color: #444; margin: 0 0 16px 0;">Copy the shortcode below and paste it into any WordPress page or post where you want the donation form to appear.</p>

		<div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
			<code id="bytesis-shortcode" style="background: #fff; border: 1px dashed #ccc; padding: 10px 20px; border-radius: 6px; font-size: 16px; font-family: 'bytesisMono', monospace; letter-spacing: 0.3px; user-select: all; cursor: text;">[bytesis_donation_form]</code>
			<button type="button" class="bytesis-btn-primary" onclick="navigator.clipboard.writeText('[bytesis_donation_form]'); this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy', 1500);" style="padding: 8px 20px; font-size: 14px;">Copy</button>
		</div>

		<p style="font-weight: 330; color: #888; font-size: 13px; margin: 0;">Works with any theme or page builder — Elementor, Gutenberg, Classic Editor, etc.</p>
	</div>

	<!-- Quick Setup Guide -->
	<div style="background: #fff; border: 1px solid #e8e8e8; border-radius: 8px; padding: 28px 32px; margin-bottom: 24px;">
		<h2 style="font-size: 18px; font-weight: 540; letter-spacing: -0.3px; margin: 0 0 20px 0;">Quick Setup Guide</h2>

		<div style="display: flex; flex-direction: column; gap: 16px;">
			<!-- Step 1 -->
			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="min-width: 32px; height: 32px; background: #000; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 540; font-size: 14px;">1</div>
				<div>
					<strong style="font-weight: 480;">Configure SSLCommerz in WooCommerce</strong>
					<p style="font-weight: 330; color: #666; margin: 4px 0 0 0; font-size: 14px;">
						Go to <a href="<?php echo esc_url( admin_url('admin.php?page=wc-settings&tab=checkout&section=sslcommerz') ); ?>">WooCommerce → Settings → Payments → SSLCommerz</a> and enter your Store ID, Password, and set your Success/Fail pages.
					</p>
				</div>
			</div>

			<!-- Step 2 -->
			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="min-width: 32px; height: 32px; background: #000; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 540; font-size: 14px;">2</div>
				<div>
					<strong style="font-weight: 480;">Create Fund Categories</strong>
					<p style="font-weight: 330; color: #666; margin: 4px 0 0 0; font-size: 14px;">
						Go to <a href="<?php echo esc_url( admin_url('admin.php?page=bytesis-donation-settings') ); ?>">Settings → Funds</a> and add the causes donors can contribute to (e.g., "Child Education", "Winter Relief"). Each fund auto-creates a hidden WooCommerce product.
					</p>
				</div>
			</div>

			<!-- Step 3 -->
			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="min-width: 32px; height: 32px; background: #000; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 540; font-size: 14px;">3</div>
				<div>
					<strong style="font-weight: 480;">Set Preset Donation Amounts</strong>
					<p style="font-weight: 330; color: #666; margin: 4px 0 0 0; font-size: 14px;">
						In <a href="<?php echo esc_url( admin_url('admin.php?page=bytesis-donation-settings') ); ?>">Settings → General</a>, set the preset amounts shown as buttons on the form (e.g., 50, 100, 250, 500). Donors can also enter a custom amount.
					</p>
				</div>
			</div>

			<!-- Step 4 -->
			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="min-width: 32px; height: 32px; background: #000; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 540; font-size: 14px;">4</div>
				<div>
					<strong style="font-weight: 480;">Add the Donation Form to a Page</strong>
					<p style="font-weight: 330; color: #666; margin: 4px 0 0 0; font-size: 14px;">
						Create or edit a WordPress page, paste <code style="background: #f3f3f3; padding: 2px 6px; border-radius: 3px; font-size: 13px;">[bytesis_donation_form]</code> into the content, and publish. The form will appear exactly where you placed the shortcode.
					</p>
				</div>
			</div>

			<!-- Step 5 -->
			<div style="display: flex; gap: 16px; align-items: flex-start;">
				<div style="min-width: 32px; height: 32px; background: #000; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 540; font-size: 14px;">5</div>
				<div>
					<strong style="font-weight: 480;">Configure Email Receipts (Optional)</strong>
					<p style="font-weight: 330; color: #666; margin: 4px 0 0 0; font-size: 14px;">
						In <a href="<?php echo esc_url( admin_url('admin.php?page=bytesis-donation-settings') ); ?>">Settings → Email</a>, enter your SMTP details and customize the receipt template. Use tags like <code style="background: #f3f3f3; padding: 2px 6px; border-radius: 3px; font-size: 13px;">{donor_name}</code>, <code style="background: #f3f3f3; padding: 2px 6px; border-radius: 3px; font-size: 13px;">{amount}</code>, <code style="background: #f3f3f3; padding: 2px 6px; border-radius: 3px; font-size: 13px;">{fund_name}</code>.
					</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Links -->
	<div style="display: flex; gap: 12px; flex-wrap: wrap;">
		<a href="<?php echo esc_url( admin_url('admin.php?page=bytesis-donation-records') ); ?>" class="bytesis-btn-primary" style="text-decoration: none; padding: 10px 24px;">View Donations</a>
		<a href="<?php echo esc_url( admin_url('admin.php?page=bytesis-donation-settings') ); ?>" class="bytesis-btn-secondary" style="text-decoration: none; padding: 10px 24px;">Plugin Settings</a>
		<a href="<?php echo esc_url( admin_url('admin.php?page=wc-settings&tab=checkout&section=sslcommerz') ); ?>" class="bytesis-btn-secondary" style="text-decoration: none; padding: 10px 24px;">SSLCommerz Settings</a>
	</div>

	<!-- Footer -->
	<p style="margin-top: 32px; font-size: 12px; font-weight: 330; color: #aaa; border-top: 1px solid #eee; padding-top: 16px;">
		Bytesis Donation Gateway v<?php echo esc_html( BYTESIS_DONATION_VERSION ); ?> — Developed by <a href="https://bytesis.com" target="_blank" style="color: #000;">Bytesis Ltd.</a>
	</p>

</div>
