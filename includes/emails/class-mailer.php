<?php
namespace Bytesis\DonationGateway;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Mailer {

	public function init() {
		add_action( 'woocommerce_order_status_completed', array( $this, 'send_receipt' ), 20, 2 );
		add_action( 'wp_ajax_bytesis_test_email', array( $this, 'send_test_email_ajax' ) );
	}

	public function send_receipt( $order_id, $order ) {
		// Only send if enabled
		if ( ! get_option( 'bytesis_donation_email_enabled' ) ) {
			return;
		}

		if ( $order->get_meta( '_is_bytesis_donation' ) !== 'yes' ) {
			return;
		}

		$donor_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$donor_email = $order->get_billing_email();
		$amount = $order->get_total();
		$fund_name = $order->get_meta( '_bytesis_fund_name' );
		$transaction_id = $order->get_transaction_id();
		$date = $order->get_date_created()->date_i18n( get_option( 'date_format' ) );

		$subject = get_option( 'bytesis_donation_email_subject', 'Thank you for your donation!' );
		$body = get_option( 'bytesis_donation_email_body', 'Thank you {donor_name} for your donation of ৳{amount} to {fund_name}.' );

		// Replace tags
		$replacements = array(
			'{donor_name}'     => $donor_name,
			'{amount}'         => $amount,
			'{fund_name}'      => $fund_name,
			'{transaction_id}' => $transaction_id,
			'{date}'           => $date,
		);

		$body = str_replace( array_keys( $replacements ), array_values( $replacements ), $body );
		
		// Wrap in HTML template
		ob_start();
		include BYTESIS_DONATION_PLUGIN_DIR . 'includes/emails/views/receipt-template.php';
		$html_message = ob_get_clean();

		$this->send_mail( $donor_email, $subject, $html_message );
	}

	private function send_mail( $to, $subject, $message ) {
		// Custom PHPMailer to avoid overriding WP's global or WC's mailer
		require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
		require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';
		require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';

		$mail = new \PHPMailer\PHPMailer\PHPMailer(true);

		try {
			$host = get_option('bytesis_donation_smtp_host');
			$port = get_option('bytesis_donation_smtp_port');
			$user = get_option('bytesis_donation_smtp_user');
			$pass = get_option('bytesis_donation_smtp_pass');

			if ( !empty($host) && !empty($user) ) {
				$mail->isSMTP();
				$mail->Host       = $host;
				$mail->SMTPAuth   = true;
				$mail->Username   = $user;
				$mail->Password   = $pass;
				$mail->SMTPSecure = ($port == 465) ? \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS : \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port       = $port;
			} else {
				$mail->isMail();
			}

			$from_email = get_option('bytesis_donation_smtp_from_email', get_option('admin_email'));
			$from_name  = get_option('bytesis_donation_smtp_from_name', get_bloginfo('name'));

			$mail->setFrom( $from_email, $from_name );
			$mail->addAddress( $to );

			$mail->isHTML(true);
			$mail->Subject = $subject;
			$mail->Body    = $message;

			return $mail->send();
		} catch ( \Exception $e ) {
			error_log('Bytesis Donation Mail Error: ' . $mail->ErrorInfo);
			return false;
		}
	}

	public function send_test_email_ajax() {
		check_ajax_referer( 'bytesis_donation_nonce', 'nonce' );
		if ( ! current_user_can('manage_options') ) {
			wp_send_json_error('Unauthorized');
		}

		$to = sanitize_email( $_POST['test_email'] );
		$success = $this->send_mail( $to, 'Test Email - Bytesis Donation', '<p>This is a test email to verify your SMTP settings.</p>' );

		if ( $success ) {
			wp_send_json_success('Test email sent successfully.');
		} else {
			wp_send_json_error('Failed to send test email. Check error logs.');
		}
	}
}
