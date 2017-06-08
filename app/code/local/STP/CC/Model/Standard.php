<?php
class STP_CC_Model_Standard extends Mage_Payment_Model_Method_Abstract {
	protected $_code = 'cc';
	
	protected $_isInitializeNeeded      = true;
	protected $_canUseInternal          = true;
	protected $_canUseForMultishipping  = false;
	
	public function getOrderPlaceRedirectUrl() {
		return Mage::getUrl('cc/payment/redirect', array('_secure' => true));
	}
}
?>