<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<style>
		body {
			font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
			background-color: #f7f7f7;
			color: #000000;
			margin: 0;
			padding: 40px 20px;
		}
		.container {
			max-width: 600px;
			margin: 0 auto;
			background-color: #ffffff;
			border-radius: 8px;
			padding: 40px;
			box-shadow: 0 2px 10px rgba(0,0,0,0.05);
		}
		.header {
			text-align: center;
			margin-bottom: 30px;
		}
		.header h1 {
			margin: 0;
			font-size: 24px;
			font-weight: 600;
			letter-spacing: -0.5px;
		}
		.content {
			font-size: 16px;
			line-height: 1.6;
		}
		.footer {
			margin-top: 40px;
			text-align: center;
			font-size: 12px;
			color: #666666;
			border-top: 1px solid #eeeeee;
			padding-top: 20px;
		}
	</style>
</head>
<body>
	<div class="container">
		<div class="header">
			<h1>Donation Receipt</h1>
		</div>
		<div class="content">
			<?php echo wp_kses_post( wpautop( $body ) ); ?>
		</div>
		<div class="footer">
			<p>Thank you for your generous support.</p>
		</div>
	</div>
</body>
</html>
