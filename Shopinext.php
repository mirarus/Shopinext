<?php

/**
 * Shopinext Pos System
 * @author  Mirarus <aliguclutr@gmail.com>
 */
class Shopinext
{

	private $config = [];
	private $customer = [];
	private $product = [];
	private $country_codes = ['TRY', 'USD', 'EUR', 'GBP'];
	private $currency_code;


	public function setConfig($data=[])
	{
		if ($data['api_key'] == null || $data['sn_id'] == null || $data['sn_password'] == null) {
			exit("Missing api information.");
		} else{
			$this->config = [
				'api_key' => $data['api_key'],
				'sn_id' => $data['sn_id'],
				'sn_password' => $data['sn_password'],
			];
		}
	}

	public function setCustomer($data=[])
	{
		if ($data['name'] == null || $data['email'] == null || $data['phone'] == null || $data['address'] == null || $data['country'] == null || $data['city'] == null || $data['postal_code'] == null) {
			exit("Missing customer information.");
		} else{
			$this->customer = [
				'name' => $data['name'],
				'email' => $data['email'],
				'phone' => $data['phone'],
				'address' => $data['address'],
				'country' => $data['country'],
				'city' => $data['city'],
				'postal_code' => $data['postal_code'],
			];
		}
	}
	
	public function setProduct($data=[])
	{
		if ($data['amount'] == null) {
			exit("Missing product information.");
		} else{
			$this->product = [
				'amount' => $data['amount']
			];
		}
	}

	public function setLocale($code)
	{
		if ($code != null) {
			if (in_array($code, $this->country_codes)) {
				$this->currency_code = $code;
			} else{
				exit("Invalid Currency Code");
			}
		}
	}
	
	public function init()
	{
		if ($this->currency_code) {
			$result = $this->Curl([
				'ACTION'=>'CURRENCY',
				'APIKEY' => $this->config['api_key'],
				'SNID' => $this->config['sn_id'],
				'SNPASS' => $this->config['sn_password'],
				'CURRENCY' => $this->currency_code
			]);
			if (isset($result['amount'])) {
				$this->product['amount'] = $this->product['amount'] * $result['amount'];
			}
		}
		return $this->Curl([
			'ACTION' => 'SESSIONTOKEN',
			'APIKEY' => $this->config['api_key'],
			'SNID' => $this->config['sn_id'],
			'SNPASS' => $this->config['sn_password'],
			'PRICE' => $this->product['amount'],
			'RETURNURL' => $this->config['return_url'],
			'CUSTOMERNAME' => $this->customer['name'],
			'CUSTOMEREMAIL' => $this->customer['email'],
			'CUSTOMERPHONE' => $this->customer['phone'],
			'CUSTOMERIP' => $this->GetIP(),
			'CUSTOMERUSERAGENT' => $_SERVER['HTTP_USER_AGENT'],
			'BILLTOADDRESSLINE' => $this->customer['address'],
			'BILLTOCOUNTRY' => $this->customer['country'],
			'BILLTOCITY' => $this->customer['city'],
			'BILLTOPOSTALCODE' => $this->customer['postal_code'],
			'BILLTOPHONE' => $this->customer['phone'],
			'SHIPTOADDRESSLINE' => $this->customer['address'],
			'SHIPTOCOUNTRY' => $this->customer['country'],
			'SHIPTOCITY' => $this->customer['city'],
			'SHIPTOPOSTALCODE' => $this->customer['postal_code'],
			'SHIPTOPHONE' => $this->customer['phone']
		]);
	}

	public function Curl($data=[])
	{
		$curl = curl_init();
		curl_setopt_array($curl, [
			CURLOPT_URL => "https://www.shopinext.com/api/v1",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_FRESH_CONNECT => true,
			CURLOPT_TIMEOUT => 20,
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => $data
		]);
		$response = curl_exec($curl);
		if (curl_errno($curl)) {
			exit('Shoinext Connection Error! <br> Error: ' . curl_error($curl));
		} else{
			return json_decode($response, true);
		}
		curl_close($curl);		
	}

	public function GetIP()
	{
		if (getenv("HTTP_CLIENT_IP")) {
			$ip = getenv("HTTP_CLIENT_IP");
		} elseif (getenv("HTTP_X_FORWARDED_FOR")) {
			$ip = getenv("HTTP_X_FORWARDED_FOR");
			if (strstr($ip, ',')) {
				$tmp = explode (',', $ip);
				$ip = trim($tmp[0]);
			}
		} else{
			$ip = getenv("REMOTE_ADDR");
		}
		return $ip;
	}
}