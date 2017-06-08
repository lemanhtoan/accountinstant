<?php
class Magestore_Onestepcheckout_Block_Sendemail extends Mage_Checkout_Block_Onepage_Abstract {
	public function __construct() {
		parent::_construct();
		$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		/*
		$orderId=$this->getRequest()->getParam('orderid');
		if (!isset($orderId))
		{
			$orderId=$this->getRequest()->getParam('comments');
		}
		if (!isset($orderId))
		{
			$orderId=$_POST['orderid'];
		}
		else{
			$orderId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
		}*/
		$order = Mage::getModel('sales/order')->loadByIncrementId($orderId);
		if($orderId && is_object($order) && !$order->getEmailSent()){
			$order->sendNewOrderEmail();
			$order->setEmailSent(true);
			$order->save();	
		}
	}
}