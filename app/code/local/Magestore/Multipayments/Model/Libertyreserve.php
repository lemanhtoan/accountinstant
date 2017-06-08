<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Multipayments
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Liberty Reserve Model
 * 
 * @category    Magestore
 * @package     Magestore_Multipayments
 * @author      Magestore Developer
 */
class Magestore_Multipayments_Model_Libertyreserve extends Mage_Payment_Model_Method_Abstract
{
    /**
    * unique internal payment method identifier
    *
    * @var string [a-z0-9_]
    */
    protected $_code = 'libertyreserve';
	
	/**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway               = true;
	
	/**
     * Can authorize online?
     */
    protected $_canAuthorize            = true;
	
	/**
     * Can capture funds online?
     */
    protected $_canCapture              = true;
	
	 /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial       = false;
	
	/**
     * Can refund online?
     */
    protected $_canRefund               = false;
	
	/**
     * Can void transactions online?
     */
    protected $_canVoid                 = true;
	
	/**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal          = true;
	
	/**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout          = true;
	
	/**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping  = true;
	
	/**
     * Can save credit card information for future processing?
     */
    protected $_canSaveCc = false;
	
	/**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }
	
	/**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }
	/**
     * Return Order place redirect url
     *
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
		$quote = $this->getQuote();
		$total = 0.01;//$quote->getGrandTotal();
		$acc = 'U4785635';
		$currency = 'LRUSD';
		$success = 'http://gochaihuoc.com/dev/index.php/multipayments/index/finish';
		$feedbackMethod = 'POST';
		$orderIncrementId = $quote->getReservedOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);
		//Zend_Debug::dump($order->getData());die('1');
		return 'https://sci.libertyreserve.com/en?lr_acc='.$acc.'&lr_amnt='.$total.'&lr_currency='.$currency.'&lr_success_url_method='.$feedbackMethod.'&lr_success_url='.$success.'&order_id='.$order->getId();
    }
}