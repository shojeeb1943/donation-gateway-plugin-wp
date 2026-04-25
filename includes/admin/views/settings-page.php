<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap bytesis-admin-wrap">
	<h1>Settings</h1>
	<p>Configure your donation gateway, funds, and email receipts.</p>

	<?php settings_errors(); ?>

	<div class="bytesis-tabs" style="margin-bottom: 24px;">
		<!-- Simple JS Tab implementation -->
		<button class="bytesis-btn-primary tab-link active" onclick="openTab(event, 'general')">General</button>
		<button class="bytesis-btn-secondary tab-link" onclick="openTab(event, 'funds')">Funds</button>
		<button class="bytesis-btn-secondary tab-link" onclick="openTab(event, 'email')">Email</button>
	</div>

	<form method="post" action="options.php">
		<?php settings_fields( 'bytesis_donation_settings' ); ?>

		<!-- GENERAL TAB -->
		<div id="general" class="tab-content" style="display:block;">
			<h2>General Configuration</h2>
			<table class="form-table">
				<tr>
					<th scope="row"><span class="bytesis-mono-label">Preset Amounts</span></th>
					<td>
						<input type="text" name="bytesis_donation_preset_amounts" value="<?php echo esc_attr( get_option('bytesis_donation_preset_amounts', '50, 100, 250, 500') ); ?>" class="regular-text" />
						<p class="description">Comma separated amounts (e.g., 50, 100, 250, 500).</p>
					</td>
				</tr>
			</table>
			<p style="margin-top: 16px; padding: 12px 16px; background: rgba(0,0,0,0.03); border-radius: 6px; font-weight: 330; letter-spacing: -0.14px;">
				<strong>Payment & Redirects:</strong> SSLCommerz credentials, sandbox/live mode, and success/fail page redirects are managed in
				<a href="<?php echo esc_url( admin_url('admin.php?page=wc-settings&tab=checkout&section=sslcommerz') ); ?>">WooCommerce → Settings → Payments → SSLCommerz</a>.
			</p>
		</div>

		<!-- EMAIL TAB -->
		<div id="email" class="tab-content" style="display:none;">
			<h2>SMTP & Email Receipt</h2>
			<table class="form-table">
				<tr>
					<th scope="row"><span class="bytesis-mono-label">Enable Emails</span></th>
					<td><input type="checkbox" name="bytesis_donation_email_enabled" value="1" <?php checked(get_option('bytesis_donation_email_enabled'), '1'); ?> /> Send receipt to donors</td>
				</tr>
				<tr>
					<th scope="row"><span class="bytesis-mono-label">SMTP Host</span></th>
					<td><input type="text" name="bytesis_donation_smtp_host" value="<?php echo esc_attr( get_option('bytesis_donation_smtp_host') ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><span class="bytesis-mono-label">SMTP Port</span></th>
					<td><input type="number" name="bytesis_donation_smtp_port" value="<?php echo esc_attr( get_option('bytesis_donation_smtp_port', '587') ); ?>" class="small-text" /></td>
				</tr>
				<tr>
					<th scope="row"><span class="bytesis-mono-label">SMTP Username</span></th>
					<td><input type="text" name="bytesis_donation_smtp_user" value="<?php echo esc_attr( get_option('bytesis_donation_smtp_user') ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><span class="bytesis-mono-label">SMTP Password</span></th>
					<td><input type="password" name="bytesis_donation_smtp_pass" value="<?php echo esc_attr( get_option('bytesis_donation_smtp_pass') ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><span class="bytesis-mono-label">From Email</span></th>
					<td><input type="email" name="bytesis_donation_smtp_from_email" value="<?php echo esc_attr( get_option('bytesis_donation_smtp_from_email') ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><span class="bytesis-mono-label">From Name</span></th>
					<td><input type="text" name="bytesis_donation_smtp_from_name" value="<?php echo esc_attr( get_option('bytesis_donation_smtp_from_name') ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><span class="bytesis-mono-label">Email Subject</span></th>
					<td><input type="text" name="bytesis_donation_email_subject" value="<?php echo esc_attr( get_option('bytesis_donation_email_subject', 'Thank you for your donation!') ); ?>" class="regular-text" /></td>
				</tr>
				<tr>
					<th scope="row"><span class="bytesis-mono-label">Email Body (HTML)</span></th>
					<td>
						<?php wp_editor( get_option('bytesis_donation_email_body'), 'bytesis_donation_email_body', array('textarea_rows' => 10) ); ?>
						<p class="description">Available tags: {donor_name}, {amount}, {fund_name}, {transaction_id}, {date}</p>
					</td>
				</tr>
			</table>
		</div>

		<div style="margin-top: 24px;">
			<?php submit_button( 'Save Settings', 'bytesis-btn-primary', 'submit', false ); ?>
		</div>
	</form>

	<!-- FUNDS TAB (Separate Form to avoid nesting) -->
	<div id="funds" class="tab-content" style="display:none;">
		<h2>Fund Categories</h2>
		<p>Add the causes or funds people can donate to. Each will automatically create a hidden WooCommerce product.</p>
		
		<form method="post" action="" style="margin-bottom: 24px;">
			<?php wp_nonce_field( 'bytesis_settings_action' ); ?>
			<input type="hidden" name="bytesis_action" value="add_fund" />
			<input type="text" name="fund_name" placeholder="Fund Name (e.g. Winter Clothes)" required style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;" />
			<button type="submit" class="bytesis-btn-primary">Add Fund</button>
		</form>

		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><span class="bytesis-mono-label">Fund Name</span></th>
					<th><span class="bytesis-mono-label">WC Product ID</span></th>
					<th><span class="bytesis-mono-label">Actions</span></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$fund_manager = new \Bytesis\DonationGateway\Fund_Manager();
				$funds = $fund_manager->get_funds();
				if ( empty( $funds ) ) {
					echo '<tr><td colspan="3">No funds configured yet.</td></tr>';
				} else {
					foreach ( $funds as $id => $fund ) {
						echo '<tr>';
						echo '<td><strong>' . esc_html( $fund['name'] ) . '</strong></td>';
						echo '<td>#' . esc_html( $fund['wc_product_id'] ) . '</td>';
						echo '<td>
							<form method="post" action="" style="display:inline;">
								' . wp_nonce_field( 'bytesis_settings_action', '_wpnonce', true, false ) . '
								<input type="hidden" name="bytesis_action" value="delete_fund" />
								<input type="hidden" name="fund_id" value="' . esc_attr( $id ) . '" />
								<button type="submit" class="button action" onclick="return confirm(\'Are you sure you want to delete this fund? This will not delete past donation records, but will remove it from the form.\');">Delete</button>
							</form>
						</td>';
						echo '</tr>';
					}
				}
				?>
			</tbody>
		</table>
	</div>

</div>

<script>
function openTab(evt, tabName) {
	evt.preventDefault();
	var i, tabcontent, tablinks;
	tabcontent = document.getElementsByClassName("tab-content");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}
	tablinks = document.getElementsByClassName("tab-link");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" bytesis-btn-primary active", " bytesis-btn-secondary");
		// Ensure any secondary classes are removed if we are making it primary later
	}
	document.getElementById(tabName).style.display = "block";
	evt.currentTarget.className = evt.currentTarget.className.replace("bytesis-btn-secondary", "bytesis-btn-primary active");
}
</script>
