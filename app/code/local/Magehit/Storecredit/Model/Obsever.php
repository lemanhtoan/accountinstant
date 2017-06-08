<?php
class Magehit_Storecredit_Model_Obsever 
{
	public function saveCredits($observer)
	{
		if (!Mage::helper('storecredit')->moduleEnabled()) {
            return;
        }

        $request = $observer->getEvent()->getRequest();
        $customer = $observer->getEvent()->getCustomer();
         
        $customer_id = $customer->getId();
        $data = $request->getPost();
		
		Mage::helper('storecredit/data')->checkInsertCustomerIdStoreCredit($customer_id, 1);
         
	    $_customer = Mage::getModel('storecredit/customer')->load($customer_id);
		$store_id = Mage::getModel('customer/customer')->load($_customer->getId())->getStoreId();
    	$oldCredit = $_customer->getCreditBalance();
    	$amount = $data['mh_storecredit_amount'];
    	$action = $data['mh_storecredit_action'];
    	$comment = $data['mh_storecredit_comment'];
    	$newCredit = $oldCredit + $amount * $action;
    	 
		if($newCredit < 0) $newCredit = 0;
    	$amount = abs($newCredit - $oldCredit);
    	
		if($amount > 0){
	    	$detail = $comment;
			$_customer->setData('credit_balance',$newCredit)->save();
	    	$balance = $_customer->getCreditBalance();
	    	
		
	    	$historyData = array('customer_id'=>$customer_id,
	    					     'transaction_type'=>($action>0)?Magehit_Storecredit_Model_Type::ADMIN_ADDITION:Magehit_Storecredit_Model_Type::ADMIN_SUBTRACT, 
						    	 'amount'=>$amount,
						    	 'balance'=>$balance, 
						    	 'transaction_params'=>$detail,
	    	                     'transaction_detail'=>$detail,
	    	                     'order_id'=>0,
						    	 'transaction_time'=>now(), 
    							 'expired_time'=>null,
            					 'remaining_credit'=>0,
						    	 'status'=>Magehit_Storecredit_Model_Statushistory::COMPLETE);
	    	
	    	Mage::getModel('storecredit/history')->setData($historyData)->save();
	 
			Mage::helper('storecredit')->sendEmailCustomerCreditChanged($_customer->getId(),$historyData, $store_id);
    	}
	}
	
	public function processOrderCreationData(Varien_Event_Observer $observer)
    {
        $model = $observer->getEvent()->getOrderCreateModel();
        $request = $observer->getEvent()->getRequest();
        $quote = $model->getQuote();
        if(isset($request['mh_storecredit_add_input']) && isset($request['customer_id'])) {
        	
            $credit = $request['mh_storecredit_add_input'];
            $customer_id = $request['customer_id'];
            if(isset($request['store_id'])) $store_id = $request['store_id'];
            else $store_id = Mage::app()->getStore()->getId();
            
            try {
            	
	    		$baseGrandTotal = $quote->getBaseGrandTotal();
	    		Mage::helper('storecredit')->setCreditToCheckOutAdmin($credit,$quote,$customer_id,$store_id,$baseGrandTotal);
	    	
		    	$quote ->collectTotals()->save();  
		    	     
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session_quote')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session_quote')->addException(
                    $e,
                    $this->__('Cannot apply Credits')
                );
            }
        }
        return $this;
    }
	public function salesConvertQuoteItemToOrderItem(Varien_Event_Observer $observer)
	{
	    $quoteItem = $observer->getItem();
	    if ($additionalOptions = $quoteItem->getOptionByCode('additional_options')) {
	        $orderItem = $observer->getOrderItem();
	        $options = $orderItem->getProductOptions();
	        $options['additional_options'] = unserialize($additionalOptions->getValue());
	        $orderItem->setProductOptions($options);
	    }
	}
	public function checkoutCartProductAddAfter($observer){
		
		$store_id = Mage::app()->getStore()->getId();
		$_product = $observer->getProduct();    	
    	$item = $observer->getQuoteItem();
		$mh_storecredit = Mage::getModel('catalog/product')->load($_product->getId())->getData('mh_storecredit');
		if($mh_storecredit >0){
			$infoArr = array();
		
		    if ($info = $item->getProduct()->getCustomOption('info_buyRequest')) {
		        $infoArr = unserialize($info->getValue());
		    }
		    if ($infoArr && isset($infoArr['additional_options'])) {
		        $item->addOption(array(
		            'code' => 'additional_options',
		            'value' => serialize($infoArr['additional_options'])
		        ));
		        return;
		    }
	    	$additionalOptions = array(array(
	                'code' => 'mh_storecredit',
	                'label' => Mage::helper('storecredit')->__('Credits'),
	                'value' => $mh_storecredit,
	                'print_value' =>  Mage::helper('storecredit')->formatCredit($mh_storecredit)
	            ));
	            $item->addOption(array(
	                'code' => 'additional_options',
	                'value' => serialize($additionalOptions),
	            ));
	       // Add replacement additional option for reorder (see above)
	       $infoArr['additional_options'] = $additionalOptions;
	
	       $info->setValue(serialize($infoArr));
	       $item->addOption($info);
		}
    	
    	
	}
	public function addPaypalRewardItem(Varien_Event_Observer $observer)
    {
        $paypalCart = $observer->getEvent()->getPaypalCart();
        if ($paypalCart && abs($paypalCart->getSalesEntity()->getMhStorecreditDiscount()) > 0.0001) {
            $salesEntity = $paypalCart->getSalesEntity();
            $paypalCart->updateTotal(
                Mage_Paypal_Model_Cart::TOTAL_DISCOUNT,
                (float)$salesEntity->getMhStorecreditDiscount(),
                Mage::helper('storecredit')->__('Credit discount %s',$salesEntity->getMhStorecreditDiscountShow())
            );
        }
    }
 	public function setCheckCreditToRefund(Varien_Event_Observer $observer)
    {
        $input = $observer->getEvent()->getRequest()->getParam('creditmemo');
        $creditmemo = $observer->getEvent()->getCreditmemo();
        if (isset($input['mh_refund_storecredit_enable'])) {
            $enable = $input['mh_refund_storecredit_enable'];
            if ($enable) {
                $creditmemo->setMhStorecreditCheckRefund(1);
            }
        }
        return $this;
    }
    
 	public function refundCreditOrder(Varien_Event_Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $order = $creditmemo->getOrder();

        $base_total_refunded = $order->getBaseTotalRefunded();
    	$check_refund = $creditmemo->getMhStorecreditCheckRefund();
    	$order_id = $order->getId();
    	$order_incrementId = $order->getIncrementId();
    	$customer_id = $order->getCustomerId();
    	$store_id = Mage::getModel('customer/customer') ->load($customer_id)->getStoreId();
    	
    	$transactions_refund = Mage::getModel('storecredit/history')->getCollection()
											->addFieldToFilter('customer_id',$customer_id)
											->addFieldToFilter('order_id',$order_id)
											->addFieldToFilter('transaction_type',Magehit_Storecredit_Model_Type::REFUND_ORDER_ADD_CREDIT);
					
    	if($check_refund && sizeof($transactions_refund) == 0){
    		
	    	$_customer = Mage::getModel('storecredit/customer')->load($customer_id);
	    	
	    	$_customer->addCredit($base_total_refunded);
	    		
	    	$transaction_detail = Mage::helper('storecredit')->__('Restore credit for refunded order #%s',$order->getIncrementId());
	    	
			$historyData = array('customer_id'=>$_customer->getId(),
	    					     'transaction_type'=>Magehit_Storecredit_Model_Type::REFUND_ORDER_ADD_CREDIT, 
						    	 'amount'=>$base_total_refunded,
						    	 'balance'=>$_customer->getCreditBalance(), 
						    	 'transaction_params'=>$order_incrementId,
	    	                     'transaction_detail'=>$transaction_detail,
	    	                     'order_id'=>$order_id,
						    	 'transaction_time'=>now(), 
    							 'expired_time'=>null,
            					 'remaining_credit'=>0,
						    	 'status'=>Magehit_Storecredit_Model_Statushistory::COMPLETE);
    
    		Mage::getModel('storecredit/history')->setData($historyData)->save();
    		Mage::helper('storecredit')->sendEmailCustomerCreditChanged($_customer->getId(),$historyData, $store_id);
    	}
    	
    	
        return $this;
    }
}