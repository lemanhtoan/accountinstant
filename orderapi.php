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
	
		Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID); 
		Mage::app('');
	
		/* Format our dates */
		$fromDate = date('Y-m-d H:i:s', strtotime($_GET["d"] .' 00:00:00'));
		$toDate = date('Y-m-d H:i:s', strtotime($_GET["d"].' 23:59:59'));
 
		/* Get the collection */
		$orders = Mage::getModel('sales/order')->getCollection()
		->addAttributeToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
		->addFieldToFilter('status', 'pending');
		
		foreach ($orders as $order): 
			echo $order->getIncrementId().',';
		endforeach; 
	}
}
catch(Exception $e)
{
    echo "exception";
}
?>

