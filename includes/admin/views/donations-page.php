<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap bytesis-admin-wrap">
	<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
		<h1 style="margin-bottom:0;">Donation Records</h1>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=bytesis-donation&bytesis_export_donations=1' ) ); ?>" class="bytesis-btn-secondary">Export CSV</a>
	</div>

	<form method="get" style="margin-bottom: 24px;">
		<input type="hidden" name="page" value="bytesis-donation" />
		<input type="text" name="s" value="<?php echo esc_attr( isset($_GET['s']) ? $_GET['s'] : '' ); ?>" placeholder="Search by name, email, or TXN ID..." style="padding: 10px 16px; border-radius: 50px; border: 1px solid #ccc; width: 300px; font-family: 'bytesisSans';" />
		<button type="submit" class="bytesis-btn-primary" style="padding: 10px 24px;">Search</button>
	</form>

	<table style="width: 100%; border-collapse: collapse; text-align: left; font-family: 'bytesisSans', sans-serif;">
		<thead>
			<tr style="border-bottom: 2px solid #e2e2e2;">
				<th style="padding: 12px 8px;"><span class="bytesis-mono-label">Donor</span></th>
				<th style="padding: 12px 8px;"><span class="bytesis-mono-label">Fund</span></th>
				<th style="padding: 12px 8px;"><span class="bytesis-mono-label">Amount</span></th>
				<th style="padding: 12px 8px;"><span class="bytesis-mono-label">Status</span></th>
				<th style="padding: 12px 8px;"><span class="bytesis-mono-label">Transaction ID</span></th>
				<th style="padding: 12px 8px;"><span class="bytesis-mono-label">Date</span></th>
			</tr>
		</thead>
		<tbody>
			<?php if ( empty($results) ) : ?>
				<tr>
					<td colspan="6" style="padding: 24px 8px; text-align: center; color: #666;">No donations found.</td>
				</tr>
			<?php else : ?>
				<?php foreach ( $results as $row ) : ?>
					<tr style="border-bottom: 1px solid #f0f0f0;">
						<td style="padding: 16px 8px;">
							<strong style="font-weight: 480;"><?php echo esc_html($row->donor_name); ?></strong><br>
							<small style="color: #666; font-weight: 330;"><?php echo esc_html($row->donor_email); ?><br><?php echo esc_html($row->donor_phone); ?></small>
						</td>
						<td style="padding: 16px 8px;"><?php echo esc_html($row->fund_name); ?></td>
						<td style="padding: 16px 8px; font-weight: 480;">৳<?php echo esc_html(number_format($row->amount, 2)); ?></td>
						<td style="padding: 16px 8px;">
							<?php 
							$status_color = ($row->status === 'completed') ? 'green' : (($row->status === 'failed') ? 'red' : 'orange');
							?>
							<span style="color: <?php echo $status_color; ?>; font-weight: 480; text-transform: capitalize;"><?php echo esc_html($row->status); ?></span>
						</td>
						<td style="padding: 16px 8px; font-family: 'bytesisMono', monospace; font-size: 12px;"><?php echo esc_html($row->transaction_id ?: '-'); ?></td>
						<td style="padding: 16px 8px; color: #666; font-weight: 330;"><?php echo esc_html(date('M j, Y g:i A', strtotime($row->created_at))); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>
</div>
