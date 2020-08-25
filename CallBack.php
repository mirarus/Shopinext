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