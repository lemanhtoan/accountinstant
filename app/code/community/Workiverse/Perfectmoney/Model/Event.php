<?php
/**
 * Perfectmoney notification processor model
 */
class Workiverse_Perfectmoney_Model_Event
{
    protected $_order = null;

    /**
     * Event request data
     * @var array
     */
    protected $_eventData = array();

    /**
     * Enent request data setter
     * @param array $data
     * @return Workiverse_Perfectmoney_Model_Event
     */
    public function setEventData(array $data)
    {
        $this->_eventData = $data;
        return $this;
    }

    /**
     * Event request data getter
     * @param string $key
     * @return array|string
     */
    public function getEventData($key = null)
    {
        if (null === $key) {
            return $this->_eventData;
        }
        return isset($this->_eventData[$key]) ? $this->_eventData[$key] : null;
    }

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Process status notification from Monebookers server
     *
     * @return String
     */
    public function processStatusEvent()
    {
        try {
            $params = $this->_validateEventData();
            $msg = '';
			$var = print_r($params, true);
            if ($params['verified'] == 1) {
                //We got a PM encripted message and has been verified
                    $msg = Mage::helper('perfectmoney')->__('The Payment has been received by Perfect Money, Batch: ' .$params['PAYMENT_BATCH_NUM']);
                    $this->_processSale($msg);
            }
            return $msg;
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        } catch(Exception $e) {
            Mage::logException($e);
        }
        return;
    }

    /**
     * Process cancelation
     */
    public function cancelEvent() {
        try {
            $this->_validateEventData(false);
            $this->_processCancel('Payment was canceled.');
			return Mage::helper('perfectmoney')->__('The order has been canceled.');
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        } catch(Exception $e) {
            Mage::logException($e);
        }
        return '';
    }

    /**
     * Validate request and return QuoteId
     * Can throw Mage_Core_Exception and Exception
     *
     * @return int
     */
    public function successEvent(){
        $this->_validateEventData(false);
        return $this->_order->getQuoteId();
    }

    /**
     * Processed order cancelation
     * @param string $msg Order history message
     */
    protected function _processCancel($msg)
    {
        $this->_order->cancel();
        $this->_order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $msg);
        $this->_order->save();
    }

    /**
     * Processes payment confirmation, creates invoice if necessary, updates order status,
     * sends order confirmation to customer
     * @param string $msg Order history message
     */
    protected function _processSale($msg)
    {
		$this->_createInvoice();
        $this->_order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $msg);
		$this->_order->setStatus(Mage_Sales_Model_Order::STATE_COMPLETE);
        // save transaction ID
        $this->_order->getPayment()->setLastTransId($params['transaction_id']);
        // send new order email
        $this->_order->sendNewOrderEmail();
        $this->_order->setEmailSent(true);
     	$this->_order->save();
    }

    /**
     * Builds invoice for order
     */
    protected function _createInvoice()
    {
        if (!$this->_order->canInvoice()) {
            return;
        }
        $invoice = $this->_order->prepareInvoice();
        $invoice->register()->capture();
        $this->_order->addRelatedObject($invoice);
    }

   //Here comes the hard thing...
    protected function _validateEventData($fullCheck = true)
    {
        // get request variables
        $params = $this->_eventData;
        if (empty($params)) {
            Mage::throwException('Request does not contain any elements.');
        }
        // check order ID
        if (empty($params['transaction_id'])
            //|| ($fullCheck == false && $this->_getCheckout()->getPerfectmoneyRealOrderId() != $params['transaction_id'])
        ) {
            Mage::throwException('Missing or invalid order ID.');
        }
        // load order for further validation
        $this->_order = Mage::getModel('sales/order')->loadByIncrementId($params['transaction_id']);
        if (!$this->_order->getId()) {
            Mage::throwException('Order not found.');
        }
        if (0 !== strpos($this->_order->getPayment()->getMethodInstance()->getCode(), 'perfectmoney')) {
            Mage::throwException('Unknown payment method.');
        }

        // make additional validation
        if ($fullCheck) {
            //By default this verification process sets 'verified' as false: 0
			$params['verified'] = 0;
            // check transaction signature
            if (empty($params['V2_HASH'])) {
                Mage::throwException('Invalid transaction signature.');
            }
            //We have our own verification process.. so don't worry!... creating the md5 hash...
			$account = strtoupper(Mage::getStoreConfig('payment/perfectmoney/pm_account'));
			$units = strtoupper($this->_order->getOrderCurrencyCode());
			$secret = strtoupper(md5(Mage::getStoreConfig('payment/perfectmoney/pm_secret')));
			$total_amnt = ($params['PAYMENT_AMOUNT'] == $this->_order->getGrandTotal()) ? $params['PAYMENT_AMOUNT'] : '0';
			$prehash = $params['PAYMENT_ID'] .":" .$account .":" .$total_amnt .":" .$units .":" .$params['PAYMENT_BATCH_NUM'] .":" .$params['PAYER_ACCOUNT'] .':' .$secret .':' .$params['TIMESTAMPGMT'];
			$final_hash = strtoupper(md5($prehash));
			//
			if ($final_hash == $params['V2_HASH']) {
				$params['verified'] = 1;
			} else {
                Mage::throwException('Hash is not valid.');
            }
        }
        return $params;
    }
}
