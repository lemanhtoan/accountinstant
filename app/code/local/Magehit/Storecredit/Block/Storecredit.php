<?php
class Magehit_Storecredit_Block_Storecredit extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    public function getCustomerId()
    {
    	return Mage::getSingleton('customer/session')->getCustomer()->getId();
    }
    public function getCreditByCheckout()
    {
    	//return Mage::helper('core')->currency(Mage::getSingleton('checkout/session')->getQuote()->getMhStorecredit(),false,false);
    	return round(Mage::helper('core')->currency(Mage::getSingleton('checkout/session')->getQuote()->getMhStorecredit(),false,false), 2);
    }
    public function getMaxCreditCheckout()
    {
    	Mage::getSingleton('checkout/session')->getQuote()->collectTotals()->save();
    	return Mage::helper('core')->currency(Mage::getSingleton('checkout/session')->getQuote()->getMhStorecreditCheckoutMax(),false,false);
    }
	public function getMinCreditCheckout()
    {
    	return Mage::helper('core')->currency(Mage::getSingleton('checkout/session')->getQuote()->getMhStorecreditCheckoutMin(),false,false);
    }
    public function getCreditUpdatedNotification()
    {
    	$customer_id = $this->getCustomerId();
    	return (int)Mage::getModel('storecredit/customer')->load($customer_id)->getCreditUpdatedNotification();
    }
}