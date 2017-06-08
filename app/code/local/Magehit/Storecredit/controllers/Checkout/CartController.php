<?php
class Magehit_Storecredit_Checkout_CartController extends Mage_Core_Controller_Front_Action
{
    private function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
    
    private function _goBack($onepage=null)
    {
    	if(is_null($onepage))
    	{
    		return $this->_redirect('checkout/cart');
    	}
    }
	public function updateformcreditAction()
    {
    	if(!(Mage::helper('storecredit')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		
    	$this->_getSession()->getQuote()->collectTotals()->save();
    	$this->loadLayout();
		$this->renderLayout();
    }
    public function onepagepostAction()
    {
    	if(!(Mage::helper('storecredit')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		
    	$onepage = 1;
    	$this->creditPostAction($onepage);
    	Mage::getSingleton('checkout/session')->getQuote()->collectTotals()->save();
    	$this->loadLayout();
		$this->renderLayout();
    }
	public function creditPostAction($onepage=null)
    {
    	if(!(Mage::helper('storecredit')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		
    	$quote = $this->_getSession()->getQuote();
    	$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode(); 
    	$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();  
    
				
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
        	if($onepage ==null) $this->_getSession()->addError($this->__('You must login to use this function'));
			$this->_goBack($onepage);
          	return;
    	}
    	
    	$max_credit = $quote->getMhStorecreditCheckoutMax();
    	$min_credit = $quote->getMhStorecreditCheckoutMin();
    	
        $credit = $this->getRequest()->getParam('mh_storecredit_value');
        
    	if($credit == 0) {
        	
            $quote->setMhStorecredit(0)->save();
            if($onepage ==null)  $this->_getSession()->addSuccess($this->__('The credit has cancelled successfully.'));
            $this->_goBack($onepage);
          	return;
            
        }
        
    	if ($credit < 0) {
			if($onepage ==null) $this->_getSession()->addError(
            	$this->__('Your entered amount (%s) is less than 0.', Mage::helper('core')->currency($credit,true,false))
          	);
          	$this->_goBack($onepage);
          	return;
		}
		
		$credit_tmp = Mage::helper('directory')->currencyConvert(1, $baseCurrencyCode, $currentCurrencyCode);
		$credit = $this->getRequest()->getParam('mh_storecredit_value')/$credit_tmp;
		
		if ($this->getRequest()->getParam('mh_remove_storecredit') == 1) {
        	
            $quote->setMhStorecredit(0)->save();
            if($onepage ==null)  $this->_getSession()->addSuccess($this->__('The credit has cancelled successfully.'));
            $this->_goBack($onepage);
          	return;
            
        }
       
		if ($credit > $max_credit) {
			if($onepage ==null) $this->_getSession()->addError(
            	$this->__('Your entered amount (%s) is greater than max credit.', Mage::helper('core')->currency($credit,true,false))
          	);
          	$this->_goBack($onepage);
          	return;
		}
		
    	if ($credit < $min_credit) {
			if($onepage ==null) $this->_getSession()->addError(
            	$this->__('Your entered amount (%s) is less than min credit.', Mage::helper('core')->currency($credit,true,false))
          	);
          	$this->_goBack($onepage);
          	return;
		}
        
       
        try {
        	$quote->setMhStorecredit($credit)->save();
            if($credit) {
        		if($onepage ==null) $this->_getSession()->addSuccess(
            		$this->__('Credit "%s" was applied successfully.', Mage::helper('core')->currency($credit,true,false))
        		);
            } else {
            	if($onepage ==null) $this->_getSession()->addSuccess($this->__('The credit has cancelled successfully.'));
        	}

        }
        catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            if($onepage ==null) $this->_getSession()->addError($this->__('Can not apply credit.'));
        }

        $this->_goBack($onepage);
    }
}
?>