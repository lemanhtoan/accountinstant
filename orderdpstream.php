<?php
require 'app/Mage.php';
error_reporting(E_ALL | E_STRICT);
ini_set('html_errors', 1);
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);

$client  = @$_SERVER['HTTP_CLIENT_IP'];
$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$remote  = $_SERVER['REMOTE_ADDR'];
$ip='';
if(filter_var($client, FILTER_VALIDATE_IP)){
    $ip = $client;
}
else if(filter_var($forward, FILTER_VALIDATE_IP)){
    $ip = $forward;
}    
else{
    $ip = $remote;
}
//&& $ip=='103.207.39.64'
try {
	if($_GET["p"]=='pass2015!@'){
		
		$url = 'https://api-reseller.dpstream.net/genKeys/8f0e878cd8bbcf623a57e75cc4b51ac9e7ac5916/'.$_GET["t"].'/';
		$ch = curl_init();
		// set url
		curl_setopt($ch, CURLOPT_URL, $url);
		//return the transfer as a string
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// $output contains the output string
		$output = curl_exec($ch);
		// close curl resource to free up system resources
		curl_close($ch);    
		//$response = json_decode($output, true);
		echo $output;
	}
}
catch(Exception $e)
{
    echo "exception";
}
?>

