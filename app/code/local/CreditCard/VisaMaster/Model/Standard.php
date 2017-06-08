<?php
class CreditCard_VisaMaster_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'VisaMaster';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	protected $_canUseCheckout = true;
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('visamaster/payment/redirect', array('_secure' => true));
	}
}
?>