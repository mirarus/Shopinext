# Shopinext
Php Class for Shopinext Virtual Pos
 
# Creating a Sample Payment Page

```
<?php

require 'Shopinext.php'; 

$shopinext = new Shopinext();

$shopinext->setConfig([
    'api_key' => 'API KEY', # Shopinext Api Key
    'sn_id' => 'E-MAIL', # Shpinext User Mail
    'sn_password' => 'PASSWORD', # Shopinext User Password
    'return_url' => 'CallBack.php', # Shopinext CallBack Url
]);

$shopinext->setCustomer([
    'name' => 'customer.name', # Customer Name
    'email' => 'customer.mail@gmail.com', # Customer Mail
    'phone' => 'customer.phone', # Customer Phone Number
    'address' => 'customer.address', # Customer Address
    'country' => 'Turkey', # Customer Country
    'city' => 'Ankara', # Customer City
    'postal_code' => '06000', # Customer Postal Code
]);

$shopinext->setProduct([
    'amount' => 100, # Product Price
]);

$shopinext->setLocale('TRY');

$result = $shopinext->init();

if (isset($result['sessionToken'])) {
    exit('Payment failed! <br> Reason: ' . $result['errorMsg']);
    $error_message = $result['errorMsg'];
} else{
    ?>
    <!DOCTYPE HTML>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <script src="https://bossanova.uk/jsuites/v3/jsuites.js"></script>
    </head>
    <body>
        <form class="pl-5 pr-5 pt-4 pb-4" action="https://www.shopinext.com/sale3d/<?php echo $result['sessionToken']; ?>" method="post" novalidate="novalidate">
            <?php if (@$error_message) {
                echo '<div class="row">Payment Error!<b>' . $error_message . '</b><br>Please check the form and try again.</div>';
            } ?>
            <div class="form-group">
                <label class="text-muted pb-2">Name Surname on Card</label>
                <input type="text" class="form-control text-center" name="name" autocomplete="cc-name" maxlength="32" required>
            </div>
            <div class="form-group">
                <label class="text-muted pb-2">Card number</label>
                <input type="tel" class="form-control text-center card-number" name="number" placeholder="•••• •••• •••• ••••" inputmode="numeric" pattern="\d{4} \d{4} \d{4} \d{4}" data-mask="0000 0000 0000 0000" autocomplete="cc-number" minlength="19" maxlength="19" required>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4 col-4">
                    <label class="text-muted pb-2">Month</label>
                    <input type="text" class="form-control text-center card-expiry-mont" name="month" placeholder="••" data-mask="mm" autocomplete="cc-exp-month" minlength="2" maxlength="2" required>
                </div>
                <div class="form-group col-md-4 col-4">
                    <label class="text-muted pb-2">Year</label>
                    <input type="text" class="form-control text-center card-expiry-year" name="year" placeholder="••••" data-mask="yyyy" autocomplete="cc-exp-year" minlength="4" maxlength="4" required>
                </div>
                <div class="form-group col-md-4 col-4">
                    <label class="text-muted pb-2">CVV</label>
                    <input type="text" class="form-control text-center card-cvc" name="cvv" placeholder="•••" data-mask="0000" autocomplete="cc-csc" minlength="3" maxlength="4" required>
                </div>
                <input type="hidden" name="installment" placeholder="0" />
            </div>
            <button type="submit" class="btn btn-info btn-block">Pay</button>
        </form>
    </body>
    </html>
    <?php
}
```

# Creating a Sample CallBack Page

```
<?php

require 'Shopinext.php';

$shopinext = new Shopinext();

$errorCode = $_POST['errorCode'];
$responseCode = $_POST['responseCode'];
$sessionToken = $_POST['sessionToken'];
$errorMsg = $_POST['errorMsg'];
$responseMsg = $_POST['responseMsg'];
$orderID = $_POST['orderID'];

if (isset($responseCode)) {
	if ($responseCode == 00) {
		$result = $shopinext->Curl([
			'ACTION' => 'ISDONE',
			'SESID' => $sessionToken
		]);
		if ($result['responseCode'] == 00) {
			# Success Action
		} else{
			# Failed Action
		}
	} elseif ($responseCode == 99) {
		# Failed Action
	}
} else{
	exit("Response Code Not Found!");
}
```