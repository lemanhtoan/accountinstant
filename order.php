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
		
		Mage::app('');
		/* Format our dates */
		$order = Mage::getModel('sales/order')->loadByIncrementId($_GET["id"]);
		
		$items = $order->getAllItems();
		foreach($items as $i):
		echo $_product = Mage::getModel('catalog/product')
            ->load($i->getProductId())->getSku().',';
		endforeach;
		
		echo $order->getCustomerEmail(). ',' . $order->getBillingAddress()->getTelephone();
	}
}
catch(Exception $e)
{
    echo "exception";
}
?>

