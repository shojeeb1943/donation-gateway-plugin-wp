<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fund_manager = new \Bytesis\DonationGateway\Fund_Manager();
$funds = $fund_manager->get_funds();
$preset_amounts_str = get_option('bytesis_donation_preset_amounts', '50, 100, 250, 500');
$preset_amounts = array_map('trim', explode(',', $preset_amounts_str));
?>

<div class="bytesis-donation-form-wrapper">

	<!-- Header -->
	<div class="bytesis-form-header">
		<h2 class="bytesis-form-title">Make a Donation</h2>
		<p class="bytesis-form-subtitle">Please fill out the form below to make a donation. Your contribution, big or small, helps us make a real difference.</p>
	</div>

	<form id="bytesis-donation-form" class="bytesis-form" method="post" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>">
		<input type="hidden" name="action" value="bytesis_process_donation" />
		<?php wp_nonce_field( 'bytesis_donation_nonce', 'bytesis_nonce' ); ?>

		<div class="bytesis-fields-group">

			<!-- Full Name -->
			<div class="bytesis-form-group">
				<label class="bytesis-label" for="bytesis_donor_name">Full Name *</label>
				<input type="text" id="bytesis_donor_name" name="donor_name" class="bytesis-input" required placeholder="Enter your full name" />
			</div>

			<!-- Email & Phone -->
			<div class="bytesis-form-row">
				<div class="bytesis-form-col">
					<label class="bytesis-label" for="bytesis_donor_email">Email *</label>
					<input type="email" id="bytesis_donor_email" name="donor_email" class="bytesis-input" required placeholder="Enter your email" />
				</div>
				<div class="bytesis-form-col">
					<label class="bytesis-label" for="bytesis_donor_phone">Phone Number *</label>
					<input type="tel" id="bytesis_donor_phone" name="donor_phone" class="bytesis-input" required value="+880" />
				</div>
			</div>

			<!-- Choose Fund -->
			<div class="bytesis-form-group">
				<label class="bytesis-label" for="bytesis_fund_id">Choose Fund</label>
				<select id="bytesis_fund_id" name="fund_id" class="bytesis-input" required>
					<?php foreach ( $funds as $id => $fund ) : ?>
						<?php if ( !empty($fund['is_active']) ) : ?>
							<option value="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $fund['name'] ); ?></option>
						<?php endif; ?>
					<?php endforeach; ?>
				</select>
			</div>

			<!-- Choose Amount -->
			<div class="bytesis-form-group">
				<label class="bytesis-label">Choose Amount</label>
				<div class="bytesis-amount-buttons">
					<?php foreach ( $preset_amounts as $amount ) : ?>
						<button type="button" class="bytesis-preset-btn" data-amount="<?php echo esc_attr( $amount ); ?>"><?php echo esc_html( $amount ); ?>৳</button>
					<?php endforeach; ?>
					<button type="button" class="bytesis-preset-btn custom-amount-btn">Custom</button>
				</div>
				<div class="bytesis-custom-amount-wrapper" style="display:none;">
					<input type="number" id="bytesis_custom_amount" class="bytesis-input" placeholder="Enter amount" min="10" />
				</div>
				<input type="hidden" id="bytesis_final_amount" name="amount" required />
			</div>

		</div>

		<!-- Donate Button -->
		<button type="submit" id="bytesis_submit_btn" class="bytesis-btn-donate">Donate Now</button>

		<!-- Terms -->
		<p class="bytesis-form-terms">By clicking on Donate Now, You accept our <a href="#">terms and conditions</a> and <a href="#">privacy policy.</a></p>
	</form>
</div>
