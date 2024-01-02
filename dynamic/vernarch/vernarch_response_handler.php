<?php
include(dirname(__DIR__) . '/config/crypto.php');

//error_reporting(0);

$post		    = $_POST['encResp'];

$workingKey	    = 'FA758434843586A264BE2F6E9F643383';
$paramString	= decrypt($post, $workingKey);
$params		    = array();

parse_str($paramString, $params);

$order_id	    = $params['order_id'] ?? uniqid('FONS');

$order_status	    = $params['order_status'];
$email		        = $params['billing_email'];
$failure_message    = $params['failure_message'];
$payment_mode	    = $params['payment_mode'];
$card_name	        = $params['card_name'];
$status_code	    = $params['status_code'];
$status_message	    = $params['status_message'];
$response_code      = $params['response_code'];
$currency           = $params['currency'];
$amount             = $params['amount'];

$debug_info = "Original: "           .
	print_r($post, true)  .
	"\nParams: "           .
	print_r($params, true) .
	"\nEND";

$fname = "/home/gbhat/vernarch_transactions/{$order_id}";
$w = file_put_contents($fname, $debug_info);
?>
<!doctype html>
<html lang="en">

<head>
	<title>Vernacular Architecture of India</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:wght@400;500;700&family=Lora:ital,wght@0,400;1,600&display=swap"
        rel="stylesheet" />
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/theme.css" rel="stylesheet">
</head>

<body>
	<nav class="navbar navbar-expand-md">
		<div class="container">
			<a class="navbar-brand" href="https://vernacular-architecture.in"><h2 class="lead">vernacular-architecture.in</h2></a>
		</div>
	</nav>
	<div class="container">
		<main>
			<div class="row g-5">
				<div class="offset-md-2 col-md-8">
					<?php
					if ($order_status == "Success") {
					?>

						<h1>Thank you for the purchase!</h1>
						<p>
							Your transaction for the purchase of "Vernacular Architecture of India" has been successful. We will start preparing the
							shipment and inform you at <?php echo $email; ?>
						</p>
					<?php
					} else {
					?>
						<h1>Sorry! Your transaction could not be completed</h1>
						<p>
							Our apologies! Your transaction for the purchase of
							"Vernacular Architecture of India" was marked as <?php echo $order_status ?>.
						</p>
						<p>
							This could be due to several reasons
							including network and connectivity
							issues. If you prefer to take this up with
							ypur Bank or Credit Card Company, the
							following information might be useful. A
							copy of this information is also available
							with us and we will try to make sure that
							you can return and complete your purchase.
						</p>
						<table class="table">
							<thead>
								<tr>
								</tr>
							</thead>
							<tbody>
								<tr>
									<th scope="row">Payment Status</th>
									<td><?php echo $order_status; ?></td>
								</tr>
								<tr>
									<th scope="row">Payment Mode and Type</th>
									<td><?php echo "$payment_mode $card_name"; ?></td>
								</tr>
								<tr>
									<th scope="row">Amount</th>
									<td colspan="2"><?php echo "$currency $amount"; ?></td>
								</tr>
								<tr>
									<th scope="row">Reason for Failure</th>
									<td><?php echo $failure_message; ?></td>
								</tr>
								<tr>
									<th scope="row">Authorization Code from Bank</th>
									<td colspan="2"><?php echo $status_code; ?></td>
								</tr>
								<tr>
									<th scope="row">Authorization Message from Bank</th>
									<td colspan="2"><?php echo "$status_message (Code: $response_code)"; ?></td>
								</tr>
							</tbody>
						</table>
					<?php
					}
					?>
				</div>
			</div>
		</main>
	</div>
</body>

</html>