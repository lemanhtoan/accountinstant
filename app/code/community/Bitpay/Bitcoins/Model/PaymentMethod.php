<?php

/**
 * The MIT License (MIT)
 * 
 * Copyright (c) 2011-2014 BitPay LLC
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class Bitpay_Bitcoins_Model_PaymentMethod extends Mage_Payment_Model_Method_Abstract
{

    /**
     * unique internal payment method identifier
     *
     * @var string [a-z0-9_]
     */
    protected $_code = 'Bitcoins';

    /**
     * Here are examples of flags that will determine functionality availability
     * of this module to be used by frontend and backend.
     *
     * @see all flags and their defaults in Mage_Payment_Model_Method_Abstract
     *
     * It is possible to have a custom dynamic logic by overloading
     * public function can* for each flag respectively
     */

    /**
     * Is this payment method a gateway (online auth/charge) ?
     */
    protected $_isGateway = true;

    /**
     * Can authorize online?
     */
    protected $_canAuthorize = true;

    /**
     * Can capture funds online?
     */
    protected $_canCapture = true;

    /**
     * Can capture partial amounts online?
     */
    protected $_canCapturePartial = false;

    /**
     * Can refund online?
     */
    protected $_canRefund = false;

    /**
     * Can void transactions online?
     */
    protected $_canVoid = false;

    /**
     * Can use this payment method in administration panel?
     */
    protected $_canUseInternal = false;

    /**
     * Can show this payment method as an option on checkout payment page?
     */
    protected $_canUseCheckout = true;

    /**
     * Is this payment method suitable for multi-shipping checkout?
     */
    protected $_canUseForMultishipping = true;

    /**
     * Can save credit card information for future processing?
     */
    protected $_canSaveCc = false;

    /**
     * BitPay - create shipment automatically after completing order? 
     */
    protected $_bpCreateShipment = false;

    //protected $_formBlockType = 'bitcoins/form';
    //protected $_infoBlockType = 'bitcoins/info';

    /**
     * Check method for processing with base currency
     * @see Mage_Payment_Model_Method_Abstract::canUseForCurrency()
     *
     * @param string $currencyCode
     * @return boolean
     */
    public function canUseForCurrency($currencyCode)
    {
        Mage::log(
            sprintf('Checking if can use currency "%s"', $currencyCode),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        $currencies = Mage::getStoreConfig('payment/Bitcoins/currencies');
        $currencies = array_map('trim', explode(',', $currencies));

        return array_search($currencyCode, $currencies) !== false;
    }

    /**
     * Can be used in regular checkout
     * @see Mage_Payment_Model_Method_Abstract::canUseCheckout()
     *
     * @return bool
     */
    public function canUseCheckout()
    {
        $helper = Mage::helper('bitpay');
		/*
        if (!$helper->hasApiKey())
        {
            Mage::log(
                'Bitpay/Bitcoins: API key not entered',
                Zend_Log::ERR,
                Mage::helper('bitpay')->getLogFile()
            );

            return false;
        }

        if (!$helper->hasTransactionSpeed())
        {
            Mage::log(
                'Bitpay/Bitcoins: Transaction Speed has not been set',
                Zend_Log::ERR,
                Mage::helper('bitpay')->getLogFile()
            );

            return false;
        }
		*/
        return $this->_canUseCheckout;
    }

    /**
     * Authorize payment method
     *
     * @param Varien_Object $payment
     * @param float         $amount
     *
     * @return Bitpay_Bitcoins_Model_PaymentMethod
     */
    public function authorize(Varien_Object $payment, $amount)
    {
        Mage::log(
            sprintf('Authorizing payment'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        if (!Mage::getStoreConfig('payment/Bitcoins/fullscreen'))
        {
            return $this->CheckForPayment($payment);
        }
        else
        {
            return $this->CreateInvoiceAndRedirect($payment, $amount);
        }
    }

    /**
     * @param Varien_Object $payment
     *
     * @return Bitpay_Bitcoins_Model_PaymentMethod
     */
    public function CheckForPayment($payment)
    {
        Mage::log(
            sprintf('Checking for payment'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        $quoteId = $payment->getOrder()->getQuoteId();
        $ipn     = Mage::getModel('Bitcoins/ipn');

        if (!$ipn->GetQuotePaid($quoteId))
        {
            // This is the error that is displayed to the customer during checkout.
            Mage::throwException("Order not paid for.  Please pay first and then Place your Order.");
            Mage::log('Order not paid for. Please pay first and then Place Your Order.', Zend_Log::CRIT, Mage::helper('bitpay')->getLogFile());
        }
        else if (!$ipn->GetQuoteComplete($quoteId))
        {
            // order status will be PAYMENT_REVIEW instead of PROCESSING
            $payment->setIsTransactionPending(true); 
        } else {
            $this->MarkOrderPaid($payment->getOrder());
        }

        return $this;
    }

    /**
     * @param Varien_Object $order
     */
    public function invoiceOrder($order)
    {
        Mage::log(
            sprintf('Invoicing order'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        try
        {
            if (!$order->canInvoice())
            {
                Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
            }

            $invoice = $order->prepareInvoice()
                ->setTransactionId(1)
                ->addComment('Invoiced automatically by Bitpay/Bitcoins/controllers/IndexController.php')
                ->register()
                ->pay();

            $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($invoice)
                ->addObject($invoice->getOrder());

            $transactionSave->save();
        }
        catch (Exception $e)
        {
            Mage::log($e->getMessage(), Zend_Log::EMERG, Mage::helper('bitpay')->getLogFile());
            Mage::logException($e);
        }
    }

    /**
     * @param $order
     */
    public function MarkOrderPaid($order)
    {
        Mage::log(
            sprintf('Marking order paid'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();

        if ($order->getTotalDue() > 0)
        {
            if (!count($order->getInvoiceCollection()))
            {
                $this->invoiceOrder($order);
            }
        }
        else
        {
            Mage::log('MarkOrderPaid called but order '. $order->getId() .' does not have a balance due.', Zend_Log::WARN, Mage::helper('bitpay')->getLogFile());
        }
    }

    /**
     * @param $order
     */
    public function MarkOrderComplete($order)
    {
        Mage::log(
            sprintf('Marking order paid'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        if ($order->getTotalDue() >= 0 && $order->canInvoice())
        {
            if ($order->hasInvoices())
            {
                foreach ($order->getInvoiceCollection() as $_eachInvoice)
                {
                    try
                    {
                        $_eachInvoice->setRequestedCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_ONLINE);
                        $_eachInvoice->capture()->save();
                    }
                    catch (Exception $e)
                    {
                        Mage::log($e->getMessage(), Zend_Log::EMERG, Mage::helper('bitpay')->getLogFile());
                        Mage::logException($e);
                    }
                }
            }
        }

        // If the $_bpCreateShipment option is set to true above, this code will
        // programmatically create a shipment for you.  By design, this will mark
        // the entire order as 'complete'.
        if(isset($_bpCreateShipment) && $_bpCreateShipment == true)
        {
            try
            {
                $shipment = $order->prepareShipment();
                if ($shipment)
                {
                    $shipment->register();
                    $order->setIsInProcess(true);
                    $transaction_save = Mage::getModel('core/resource_transaction')
                        ->addObject($shipment)
                        ->addObject($shipment->getOrder())
                        ->save();
                }
            }
            catch (Exception $e)
            {
                Mage::log('Error creating shipment for order '. $order->getId() .'.', Zend_Log::ERR, Mage::helper('bitpay')->getLogFile());
                Mage::logException($e);
            }
        }

        try
        {
            if((isset($_bpCreateShipment) && $_bpCreateShipment == true) || Mage::getStoreConfig('payment/Bitcoins/order_disposition'))
            {
                $order->setState('Complete', 'complete', 'Completed by BitPay payments.', true);
            }
            else
            {
                $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, 'processing', 'BitPay has confirmed the payment.', false);
            }

            if(!$order->getEmailSent())
            {
                $order->sendNewOrderEmail();
            }

            $order->save();
        }
        catch (Exception $e)
        {
            Mage::log($e->getMessage(), Zend_Log::EMERG, Mage::helper('bitpay')->getLogFile());
            Mage::logException($e);
        }

    }

    /**
     * @param $order
     */
    public function MarkOrderCancelled($order)
    {
        Mage::log(
            sprintf('Marking order cancelled'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        try
        {
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
        }
        catch (Exception $e)
        {
            Mage::log('Could not cancel order '. $order->getId() .'.', null, Mage::helper('bitpay')->getLogFile());
            Mage::logException($e);
        }
    }

    /**
     * given Mage_Core_Model_Abstract, return api-friendly address
     *
     * @param $address
     *
     * @return array
     */
    public function ExtractAddress($address)
    {
        Mage::log(
            sprintf('Extracting addess'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        $options              = array();
        $options['buyerName'] = $address->getName();

        if ($address->getCompany())
        {
            $options['buyerName'] = $options['buyerName'].' c/o '.$address->getCompany();
        }

        $options['buyerAddress1'] = $address->getStreet1();
        $options['buyerAddress2'] = $address->getStreet2();
        $options['buyerAddress3'] = $address->getStreet3();
        $options['buyerAddress4'] = $address->getStreet4();
        $options['buyerCity']     = $address->getCity();
        $options['buyerState']    = $address->getRegionCode();
        $options['buyerZip']      = $address->getPostcode();
        $options['buyerCountry']  = $address->getCountry();
        $options['buyerEmail']    = $address->getEmail();
        $options['buyerPhone']    = $address->getTelephone();

        // trim to fit API specs
        foreach(array('buyerName', 'buyerAddress1', 'buyerAddress2', 'buyerAddress3', 'buyerAddress4', 'buyerCity', 'buyerState', 'buyerZip', 'buyerCountry', 'buyerEmail', 'buyerPhone') as $f)
        {
            $options[$f] = substr($options[$f], 0, 100); 
        }

        return $options;
    }

    /**
     * @param $payment
     * @param $amount
     *
     * @return Bitpay_Bitcoins_Model_PaymentMethod
     */
    public function CreateInvoiceAndRedirect($payment, $amount)
    {
        Mage::log(
            sprintf('Creating invoice and redirecting'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        include Mage::getBaseDir('lib').'/bitpay/bp_lib.php';

        $apiKey  = Mage::getStoreConfig('payment/Bitcoins/api_key');
        $speed   = Mage::getStoreConfig('payment/Bitcoins/speed');
        $order   = $payment->getOrder();
        $orderId = $order->getIncrementId();
        $options = array(
            'currency'          => $order->getBaseCurrencyCode(),
            'buyerName'         => $order->getCustomerFirstname().' '.$order->getCustomerLastname(),
            'fullNotifications' => 'true',
            'notificationURL'   => Mage::getUrl('bitpay_callback'),
            'redirectURL'       => Mage::getUrl('checkout/onepage/success'),
            'transactionSpeed'  => $speed,
            'apiKey'            => $apiKey,
        );

        /**
         * Some merchants are using custom extensions where the shipping
         * address may not be set, this will only extract the shipping
         * address if there is one already set.
         */
        if ($order->getShippingAddress()) {
            $options = array_merge(
                $options,
                $this->ExtractAddress($order->getShippingAddress())
            );
        }
        $invoice  = bpCreateInvoice($orderId, $amount, array('orderId' => $orderId), $options);
        $payment->setIsTransactionPending(true); // status will be PAYMENT_REVIEW instead of PROCESSING

		$items = array();
		foreach ($order->getAllItems() as $item) {
			$items[$item->getId()] = $item->getQtyOrdered();
		}	

        if (array_key_exists('error', $invoice))
        {
            Mage::log('Error creating bitpay invoice', Zend_Log::CRIT, Mage::helper('bitpay')->getLogFile());
            Mage::log($invoice['error'], Zend_Log::CRIT, Mage::helper('bitpay')->getLogFile());
            Mage::throwException("Error creating BitPay invoice.  Please try again or use another payment option.");
        }
        else
        {
            //$invoiceId = Mage::getModel('sales/order_invoice_api')->create($orderId, $items,null,false,true);
            Mage::getSingleton('customer/session')->setRedirectUrl($invoice['url']);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderPlaceRedirectUrl()
    {
        Mage::log(
            sprintf('Getting order place redirect url'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        if (Mage::getStoreConfig('payment/Bitcoins/fullscreen'))
        {
            return Mage::getSingleton('customer/session')->getRedirectUrl();
        }
        else
        {
            return '';
        }
    }

    /**
     * computes a unique hash determined by the contents of the cart
     *
     * @param string $quoteId
     *
     * @return boolean|string
     */
    public function getQuoteHash($quoteId)
    {
        Mage::log(
            sprintf('Getting the quote hash'),
            Zend_Log::DEBUG,
            Mage::helper('bitpay')->getLogFile()
        );

        $quote = Mage::getModel('sales/quote')->load($quoteId, 'entity_id');
        if (!$quote)
        {
            Mage::log('getQuoteTimestamp: quote not found', Zend_Log::ERR, Mage::helper('bitpay')->getLogFile());

            return false;
        }

        // encode items
        $items       = $quote->getAllItems();
        $latest      = NULL;
        $description = '';

        foreach ($items as $i)
        {
            $description.= 'i'.$i->getItemId().'q'.$i->getQty();
            // could encode $i->getOptions() here but item ids are incremented if options are changed
        }

        $hash = base64_encode(hash_hmac('sha256', $description, $quoteId));
        $hash = substr($hash, 0, 30); // fit it in posData maxlen

        return $hash;
    }
}
