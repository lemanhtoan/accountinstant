<?php

$ExternalLibPath = Mage::getModuleDir('', 'Egopay_Egopay') . DS . 'Lib' . DS . 'EgoPaySci.php';
require_once($ExternalLibPath);

class Egopay_Egopay_Model_Checkout extends Mage_Payment_Model_Method_Abstract {

    protected $_code = 'egopay';
    protected $_formBlockType = 'egopay/form';
    protected $_infoBlockType = 'egopay/info';
    protected $_order;

    const STORE_ID = 'payment/egopay/egopay_storeid';
    const STORE_PASSWORD = 'payment/egopay/egopay_storepassword';

    public function getCheckout() {
        return Mage::getSingleton('checkout/session');
    }

    public function getOrderPlaceRedirectUrl() {
        return Mage::getUrl('egopay/redirect', array('_secure' => true));
    }

    public function getEgopayUrl() {
        $url = 'https://www.egopay.com/api/pay';
        return $url;
    }

    public function getLocale() {
        return Mage::app()->getLocale()->getLocaleCode();
    }

    public function getEgopayCheckoutFormFields() {

        $order_id = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        if ($order->getBillingAddress()->getEmail()) {
            $email = $order->getBillingAddress()->getEmail();
        } else {
            $email = $order->getCustomerEmail();
        }

        try {
            $oEgopay = new EgoPaySci(array(
                        'store_id' => Mage::getStoreConfig(Egopay_Egopay_Model_Checkout::STORE_ID),
                        'store_password' => Mage::getStoreConfig(Egopay_Egopay_Model_Checkout::STORE_PASSWORD)
                    ));
            $aParams = $oEgopay->createHash(array(
                'amount' => trim(round($order->getGrandTotal(), 2)),
                'currency' => $order->getOrderCurrencyCode(),
                'description' => 'Order id:' . $order_id,
                'success_url' => Mage::getUrl('egopay/redirect/success', array('transaction_id' => $order_id)),
                'fail_url' => Mage::getUrl('egopay/redirect/cancel', array('transaction_id' => $order_id)),
                'callback_url' => Mage::getUrl('egopay/ipn'),
                'cf_1' => $order_id
                    ));
        } catch (EgoPayException $e) {
            die($e->getMessage());
        }

        $params = array(
            'hash' => $aParams
        );
        return $params;
    }

    public function initialize($paymentAction, $stateObject) {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus('pending_payment');
        $stateObject->setIsNotified(false);
    }

}
