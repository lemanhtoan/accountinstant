<?php

class Magehit_Storecredit_Block_Adminhtml_Sales_Order_Create_Payment extends Mage_Core_Block_Template
{
    
    protected function _getOrderCreateModel()
    {
        return Mage::getSingleton('adminhtml/sales_order_create');
    }
    
	public function getStoreId()
    {
        $quote = $this->_getOrderCreateModel()->getQuote();;
        return $quote->getStore()->getId();
    }
	public function getCurrentCredits()
	{
		$quote = $this->_getOrderCreateModel()->getQuote();
        $customer_id = $quote->getCustomerId();
        $store_id = $quote->getStore()->getId();
        Mage::helper('storecredit')->checkAndInsertCustomerId($customer_id);
		$customer = Mage::getModel('storecredit/customer')->load($customer_id);
		$credits = number_format($customer->getCreditBalance(),2);
		
		return $credits;
		
	}

    public function getCredits()
    {
        $quote = $this->_getOrderCreateModel()->getQuote();
        //mage::log($quote->getMhStorecredit());
        $credits = number_format($quote->getMhStorecredit(),2);
        
        return $credits;
    }
 	public function getMaxCreditToCheckOut()
    {
    	$quote = $this->_getOrderCreateModel()->getQuote();
    	$quote ->collectTotals()->save();
        /*$store_id = $quote->getStore()->getId();
        $customer_id = $quote->getCustomerId();
        $baseGrandTotal = $quote->getBaseGrandTotal();
		$spend_credit = Mage::helper('storecredit')->getMaxCreditToCheckOut($baseGrandTotal,$quote,$customer_id,$store_id);
		*/
		$spend_credit = $quote->getMhStorecreditCheckoutMax();
		
        return $spend_credit;
    }
	public function getMinCreditToCheckOut()
    {
    	$quote = $this->_getOrderCreateModel()->getQuote();
    	$quote ->collectTotals()->save();
 
		$min_spend_credit = $quote->getMhStorecreditCheckoutMin();
		
        return $min_spend_credit;
    }
}
