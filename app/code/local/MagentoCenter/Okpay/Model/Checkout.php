<?php


class MagentoCenter_Okpay_Model_Checkout extends Mage_Payment_Model_Method_Abstract {

    protected $_code          = 'okpay';
    protected $_formBlockType = 'okpay/form';
    protected $_infoBlockType = 'okpay/info';
    protected $_order;
    
    const     WALLET_ID       = 'payment/okpay/okpay_walletid';


    

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('okpay/redirect', array('_secure' => true));
    }

    public function getOkpayUrl() {
        $url = 'https://www.okpay.com/process.html';
        return $url;
    }

    public function getLocale()
    {
        return Mage::app()->getLocale()->getLocaleCode();
    }
    
    public function getOkpayCheckoutFormFields() {

        $order_id = $this->getCheckout()->getLastRealOrderId();
        $order    = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        if ($order->getBillingAddress()->getEmail()) {
            $email = $order->getBillingAddress()->getEmail();
        } else {
            $email = $order->getCustomerEmail();
        }

        $params = array(
            'ok_receiver'           => Mage::getStoreConfig(MagentoCenter_Okpay_Model_Checkout::WALLET_ID),
            'ok_invoice'            => $order_id,
            'ok_return_success'     => Mage::getUrl('okpay/redirect/success', array('transaction_id' => $order_id)),
            'ok_return_fail'        => Mage::getUrl('okpay/redirect/cancel', array('transaction_id' => $order_id)),
            'ok_language'           => $this->getLocale(),
            'ok_item_1_name'        => Mage::helper('okpay')->__('Payment for order #').$order_id,
            'ok_item_1_price'       => trim(round($order->getGrandTotal(), 2)),
            'ok_currency'           => $order->getOrderCurrencyCode(),
            'ok_ipn'           		=> Mage::getUrl('okpay/ipn'),
            'ok_payer_first_name'   => $order->getBillingAddress()->getFirstname(),
            'ok_payer_last_name'   =>  $order->getBillingAddress()->getLastname(),
            'ok_payer_email'        => $email,
        );
        return $params;
        
    }

    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

}
