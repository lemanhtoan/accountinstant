<?php
class Mage_Core_Helper_Mhstorecredit extends Mage_Core_Helper_Abstract
{
	public function moduleEnabled()
	{
		return Mage::getStoreConfig('storecredit/config/enabled');
	}
	
	public function formOnepageCheckoutCredits()
	{
		$enable = $this->moduleEnabled();
		$template = Mage::app()->getLayout()->createBlock('storecredit/storecredit')->setTemplate('mh_storecredit/checkout/onepage/credit.phtml')->toHtml();
		if(Mage::helper('core')->isModuleEnabled('Magehit_Storecredit') && $enable)  
			return '<div  id="mh-checkout-payment-storecredit">'.$template.'</div>';
		else return '';
	}
	public function formShoppingCartCredits()
	{
		$enable = $this->moduleEnabled();
		$template = Mage::app()->getLayout()->createBlock('storecredit/storecredit')->setTemplate('mh_storecredit/checkout/cart/credit.phtml')->toHtml();
		if(Mage::helper('core')->isModuleEnabled('Magehit_Storecredit') && $enable)  
			return $template;
		else return '';
	}
	public function buyCreditOnepageReview()
	{
		$enable = $this->moduleEnabled();
		if(Mage::helper('core')->isModuleEnabled('Magehit_Storecredit') && $enable)
			return Mage::app()->getLayout()->createBlock('core/template')->setTemplate('mh_storecredit/checkout/onepage/review/totals/buy_credit.phtml')->toHtml();
		else return '';
	}
	public function buyCreditShoppingCart()
	{
		$enable = $this->moduleEnabled();
		if(Mage::helper('core')->isModuleEnabled('Magehit_Storecredit') && $enable)
			return Mage::app()->getLayout()->createBlock('core/template')->setTemplate('mh_storecredit/checkout/cart/buy_credit.phtml')->toHtml();
		else return '';
	}
	public function buyCreditCreateOrderAmin()
	{
		$enable = $this->moduleEnabled();
		if(Mage::helper('core')->isModuleEnabled('Magehit_Storecredit') && $enable)
			return Mage::app()->getLayout()->createBlock('adminhtml/sales_order_create_totals')->setTemplate('mh_storecredit/sales/order/create/buy_credit.phtml')->toHtml();
		else return '';
	}
    
    public function displayCreditListProduct($_product)
    {
        if(Mage::helper('core')->isModuleEnabled('Magehit_Storecredit'))  
            return Mage::helper('storecredit/data')->getDisplayCreditListProduct($_product);
        return '';
    }
    
    public function displayCreditViewProduct($_product)
    {
        if(Mage::helper('core')->isModuleEnabled('Magehit_Storecredit'))  
            return Mage::helper('storecredit/data')->displayCreditViewProduct($_product);
        return '';
    }
}