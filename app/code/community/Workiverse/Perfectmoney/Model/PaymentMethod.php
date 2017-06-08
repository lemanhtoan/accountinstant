<?php

class Workiverse_Perfectmoney_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{
    /**
     * unique internal payment method identifier
     */
    protected $_code = 'perfectmoney';

    protected $_formBlockType = 'perfectmoney/form';
    protected $_infoBlockType = 'perfectmoney/info';
    protected $_paymentBlockType = 'perfectmoney/payment';
    /**
     * Availability options
     */
    protected $_isGateway              = true;
    protected $_canAuthorize           = true;
    protected $_canCapture             = true;
    protected $_canCapturePartial      = false;
    protected $_canRefund              = false;
    protected $_canVoid                = false;
    protected $_canUseInternal         = false;
    protected $_canUseCheckout         = true;
    protected $_canUseForMultishipping = false;
    protected $_order; 
	
    /**
     * Get order model
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->getInfoInstance()->getOrder();
        }

        return $this->_order;
    }

    /**
     * Return url for redirection after order placed
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        return Mage::getUrl('perfectmoney/processing/payment');
    }

    /**
     * Capture payment through Perfectmoney api
     *
     * @param Varien_Object $payment
     * @param decimal $amount
     * @return Workiverse_Perfectmoney_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        $payment->setStatus(self::STATUS_APPROVED)
            ->setTransactionId($this->getTransactionId())
            ->setIsTransactionClosed(0);

        return $this;
    }

    /**
     * Camcel payment
     *
     * @param Varien_Object $payment
     * @return Workiverse_Perfectmoney_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        $payment->setStatus(self::STATUS_DECLINED)
            ->setTransactionId($this->getTransactionId())
            ->setIsTransactionClosed(1);

        return $this;
    }

    /**
     * Return url of payment method
     *
     * @return string
     */
    public function getUrl()
    {
         return 'https://perfectmoney.is/api/step1.asp';
    }

    /**
     * Return url of payment method
     *
     * @return string
     */
    public function getLocale()
    {
        $locale = explode('_', Mage::app()->getLocale()->getLocaleCode());
        if (is_array($locale) && !empty($locale) && in_array($locale[0], $this->_supportedLocales)) {
            return $locale[0];
        }
        return $this->getDefaultLocale();
    }

    /**
     * prepare params array to send it to gateway page via POST
     *
     * @return array
     */
    public function getFormFields()
    {
        $order_id = $this->getOrder()->getRealOrderId();
        $billing  = $this->getOrder()->getBillingAddress();
        if ($this->getOrder()->getBillingAddress()->getEmail()) {
            $email = $this->getOrder()->getBillingAddress()->getEmail();
        } else {
            $email = $this->getOrder()->getCustomerEmail();
        }

        $params = array(
            'PAYEE_ACCOUNT'         => Mage::getStoreConfig('payment/perfectmoney/pm_account'),
            'PAYEE_NAME'            => Mage::getStoreConfig('payment/perfectmoney/pm_store_name'),
			'ORDER_NUM'             => $order_id,
            'PAYMENT_URL'           => Mage::getUrl('perfectmoney/processing/success', array('transaction_id' => $order_id)),
            'NOPAYMENT_URL'         => Mage::getUrl('perfectmoney/processing/cancel', array('transaction_id' => $order_id)),
            'STATUS_URL'            => Mage::getUrl('perfectmoney/processing/status', array('transaction_id' => $order_id)),
            'PAYMENT_AMOUNT'        => round($this->getOrder()->getGrandTotal(), 2),
            'PAYMENT_UNITS'         => $this->_order->getOrderCurrencyCode(),
        );

        return $params;
    }
    /**
     * Get initialized flag status
     * @return true
     */
    public function isInitializeNeeded()
    {
        return true;
    }

    /**
     * Instantiate state and set it to state onject
     * //@param
     * //@param
     */
    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState($state);
        $stateObject->setStatus(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setIsNotified(false);
    }

    /**
     * Get config action to process initialization
     *
     * @return string
     */
    public function getConfigPaymentAction()
    {
        $paymentAction = $this->getConfigData('payment_action');
        return empty($paymentAction) ? true : $paymentAction;
    }
}