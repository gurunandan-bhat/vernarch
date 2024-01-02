<?php
include_once(dirname(__DIR__) . '/config/config.php');
include(dirname(__DIR__) . '/config/crypto.php');

session_start();

$sender = parse_url($_SERVER['HTTP_REFERER'])['host'];

$postage_india      = 250;
$postage_anywhere   = 3500;

if (($_SERVER['REQUEST_METHOD'] == 'POST') &&
    isset($_SERVER['HTTP_REFERER']) &&
    preg_match("/". HOST_VERNARCH ."$/", $sender)
) {

    $name       = $_POST['firstName'] . ' ' . $_POST['lastName'];
    $email      = $_POST['email'];
    $address    = $_POST['address'];
    $address2   = $_POST['address2'];
    $city       = $_POST['city'];
    $state      = $_POST['state'];
    $country    = $_POST['country'];
    $zip        = $_POST['zip'];

    $copies     = (int) $_POST['copies'];

    // Check if the number of copies is numeric/integere
    $PRICE_SPEC = array(
        '1'   => 4000,
        '3'   => 3800,
        '5'   => 3600,
        '9'   => 3400,
    );

    $pp_copy = $PRICE_SPEC['1'];
    $price   = $copies * $pp_copy;
    foreach ($PRICE_SPEC as $key => $value) {

        if ($copies >= (int) $key) {

            $price   = $copies * $value;
            $pp_copy = $value;
        }
    }

	$postage = $postage_anywhere;
    if ($country == 'India') {
        $postage = $postage_india;
    }
    $postage = $copies * $postage;

    $total_charge = number_format((float)($price + $postage), 2, '.', '');

    $order_id = uniqid('VERNARCH');

    $site = "https://mariodemiranda.com";
    $CCAV_REQUEST = array(
        'merchant_id'       => '15070',
        'order_id'          => $order_id,
        'currency'          => 'INR',
        'amount'            => $total_charge,
        'redirect_url'      => $site . '/vernarch/vernarch_response_handler.php',
        'cancel_url'        => $site . '/vernarch/vernarch_response_handler.php',
        'language'          => 'en',
        'billing_name'      => $name,
        'billing_address'   => $address . '; ' . $address2,
        'billing_city'      => $city,
        'billing_state'     => $state,
        'billing_zip'       => $zip,
        'billing_country'   => $country,
        'billing_email'     => $email,
        'delivery_name'     => $name,
        'delivery_address'  => $address . '; ' . $address2,
        'delivery_city'     => $city,
        'delivery_state'    => $state,
        'delivery_zip'      => $zip,
        'delivery_country'  => $country,
    );

    $params         = '';
    $working_key    = 'FA758434843586A264BE2F6E9F643383'; //Shared by CCAVENUES
    $access_code    = 'QUK4LJVWZHIELYFM'; //Shared by CCAVENUES

    foreach ($CCAV_REQUEST as $key => $value) {
        $params .= $key . '=' . $value . '&';
    }

    $encrypted_data  = encrypt($params, $working_key);
    $transact_url    = "https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction";
} else {
    print "Access Error: Disallowed";
    exit();
}
?>
<!doctype html>
`<html lang="en">

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
    <nav class="navbar navbar-expand-md my-5">
        <div class="container">
            <a class="navbar-brand" href="https://vernacular-architecture.in"><h2  class="lead">vernacular-architecture.in</h2></a>
        </div>
    </nav>
    <div class="container">
        <main>
            <div class="row g-5">
                <div class="offset-md-2 col-md-8">
                    <h1>Thank you for the order!</h1>
                    <h2>Order Details</h2>
                    <p>
                        Thank you for placing an order for
                        <?php echo $copies . ' ' . ($copies > 1 ? 'copies' : 'copy'); ?>
                        at INR <?php echo $pp_copy; ?> per copy. We are certain that you will enjoy the writing and the beautiful photographs of traditional Indian architecture in the book.
                    </p>
                    <p>
                        All prices below are in INR (Indian Rupee
                        ₹). If you choose to proceed to our secure
                        payment gateway, this price will be
                        converted to your local currency with the
                        rate as laid down by your Bank or
                        Credit/Debit Card
                    </p>
                    <hr />
                    <p>
                        Your Order Details are:
                    </p>
                    <table class="table table-sm table-borderless">
                        <tbody>
                            <tr>
                                <th scope="row">Deliver To:</th>
                                <td><?php echo $name; ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><?php echo $address; ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><?php echo $address2; ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><?php echo $city . ', ' . $state . ' ' . $zip; ?></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td><?php echo $country; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Email For shipping updates:</th>
                                <td><?php echo $email; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Price for copies (in INR):</th>
                                <td><?php echo '(INR) ₹ ' . $price; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Postage (in INR):</th>
                                <td><?php echo '(INR) ₹ ' . $postage; ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Total to be Charged (in INR):</th>
                                <td><?php echo '(INR) ₹ ' . $total_charge; ?></td>
                            </tr>
                    </table>
                    <form method="post" action="<?php echo $transact_url ?>" novalidate>
                        <?php
                        $input_list = '';
                        foreach ($CCAV_REQUEST as $key => $value) {
                            $input_list .= '<input type="hidden" name="' . $key . '" value="' . $value . '">' . "\n";
                        }
                        echo $input_list;
                        ?>
                        <input type="hidden" name="encRequest" value="<?php echo $encrypted_data; ?>">
                        <input type="hidden" name="access_code" value="<?php echo $access_code; ?>">
                        <button class="btn btn-danger btn-md" type="submit">Continue and Pay</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="js/bootstrap.bundle.min.js"></script>
</body>

</html>