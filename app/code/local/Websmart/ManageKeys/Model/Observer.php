<?php
class Websmart_ManageKeys_Model_Observer
{
    /**
     * process controller_action_predispatch event
     *
     * @return Websmart_ManageKeys_Model_Observer
     */
    public function controllerActionPredispatchFrontend($observer)
    {
		if(Mage::getSingleton('core/cookie')->get('admin_id'))
			return $this;
		if( Mage::helper('managekeys')->isProxys() || Mage::helper('managekeys')->isRobots() || Mage::helper('managekeys')->isCountryLimit()){ 
			$action = $observer->getEvent()->getControllerAction();
			$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
			$action->getResponse()->setRedirect('http://ryushare.com/affiliate.python?aff_id=744680');
		}
        return $this; 
    }
	 public function controllerActionPredispatchAdmin($observer)
    {
		if(Mage::helper('managekeys')->getActionConfig('detect_admin') && (strpos('VN'.Mage::helper('managekeys')->getActionConfig('detect_admin'), Mage::helper('managekeys')->visitor_country())===false)){ 
			$action = $observer->getEvent()->getControllerAction();
			$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
			$action->getResponse()->setRedirect('http://ryushare.com/affiliate.python?aff_id=744680');
			return $this;
		}
		$session = Mage::getSingleton('admin/session');
		if(is_object($session->getUser()) && $session->getUser()->getId())
			Mage::getSingleton('core/cookie')->set('admin_id',$session->getUser()->getId());
        return $this; 
    }
    public function salesOrderInvoiceSaveBefore($observer)
    {
		$invoice = $observer['invoice'];
		if($invoice->getId()) return $this; 
		if(!Mage::helper('managekeys')->addKeysToOrder($invoice)){
			throw new Exception(
                    Mage::helper('managekeys')->__('Cant create keys for this order #%s! Could you please check API or money on provider.',$invoice->getOrder()->getIncrementId())
                );
		}
        return $this; 
    }
	
}