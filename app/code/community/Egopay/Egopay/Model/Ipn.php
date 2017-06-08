<?php

$ExternalLibPath = Mage::getModuleDir('', 'Egopay_Egopay') . DS . 'Lib' . DS . 'EgoPaySci.php';
require_once($ExternalLibPath);

class Egopay_Egopay_Model_Ipn {

    const STORE_ID = 'payment/egopay/egopay_storeid';
    const STORE_PASSWORD = 'payment/egopay/egopay_storepassword';

    protected $_response = array();
    protected $_error = array();
    protected $_order = null;

    public function getRequestData($key = null) {
        if (null === $key) {
            return $this->_response;
        }
        return isset($this->_response[$key]) ? $this->_response[$key] : null;
    }

    //checks if order exists and puts order into $_order
    protected function _getOrder() {
        $this->_order = Mage::getModel('sales/order')->loadByIncrementId($this->_response['cf_1']);
        if (!empty($this->_order)) {
            return true;
        } else {
            return false;
        }
    }

    //checks if order details (currency and amount) are correct
    protected function _verifyOrder() {
        $OrderAmount = $this->_order->getGrandTotal();
        $OrderCurrency = $this->_order->getOrderCurrencyCode();
        if ($OrderAmount == $this->_response['fAmount']) {
            if ($OrderCurrency == $this->_response['sCurrency']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function _registerPaymentCanceled()
    {
        $comment = 'Payment has been canceled';
        $this->_order->addStatusHistoryComment($comment, 'Canceled');
        $this->_order->save();
    }

    protected function _registerPaymentPending()
    {
        $comment = 'Payment is pending';
        $this->_order->addStatusHistoryComment($comment, 'Pending');
        $this->_order->save();
    }

    protected function _registerPaymentOnHold()
    {
        $comment = 'Payment has been holded';
        $this->_order->addStatusHistoryComment($comment, 'On Hold');
        $this->_order->save();
    }

    //Checks if payment is completed (or test) or canceled
    protected function _processOrder() {
        $this->_order = null;
        $this->_getOrder();

        $paymentStatus = $this->_response['sStatus'];
        switch ($paymentStatus) {
            case 'Completed':
            case 'TEST SUCCESS':
                $this->_processOrderStatus();
                break;

            case 'Canceled':
                $this->_registerPaymentCanceled();
                break;
            case 'On Hold':
                $this->_registerPaymentOnHold();
                break;
            case 'Pending':
                $this->_registerPaymentPending();
                break;

            default:
        }
    }

    private function _processOrderStatus() {
        $statusMessage = '';
        $payment = $this->_order->getPayment();
        $payment->setTransactionId($this->getRequestData('sId'))
                ->setPreparedMessage('Payment ' . $this->getRequestData('sId') . $this->getRequestData('sId'))
                ->setIsTransactionClosed(true)
                ->registerCaptureNotification($this->getRequestData('fAmount'));
        $this->_order->save();

        // notify customer
        $invoice = $payment->getCreatedInvoice();
        if ($invoice && !$this->_order->getEmailSent()) {
            $this->_order->sendNewOrderEmail()->addStatusHistoryComment('Payment for order: ' . $this->getRequestData('cf_1') . 'received')
                    ->setIsCustomerNotified(true)
                    ->save();
        }
    }

    public function processIpnRequest() {
        try {
            $EgoPay = new EgoPaySciCallback(array(
                        'store_id' => Mage::getStoreConfig(Egopay_Egopay_Model_Checkout::STORE_ID),
                        'store_password' => Mage::getStoreConfig(Egopay_Egopay_Model_Checkout::STORE_PASSWORD)
                    ));
            $this->_response = $EgoPay->getResponse($_POST);
        } catch (EgoPayException $e) {
            die($e->getMessage());
        }
        if ($this->_getOrder()) {
            if ($this->_verifyOrder()) {
                $this->_processOrder();
            }
        }
    }

}