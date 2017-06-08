<?php
class Magehit_Storecredit_Model_Checkout extends Mage_Core_Model_Abstract
{
    public function placeAfter($argv)
    {
    	if(Mage::helper('storecredit')->moduleEnabled())
		{
			
			$order = $argv->getOrder();
			$quote = $argv->getQuote();
			
            if (!$quote instanceof Mage_Sales_Model_Quote) {
                $quote = Mage::getModel('sales/quote')
                        ->setSharedStoreIds(array($order->getStoreId()))
                        ->load($order->getQuoteId());
            }
            
			$customer_id = $order->getCustomerId();
			$store_id = Mage::app()->getStore()->getId();
		
			$order_id = $order->getId();
			$order_incrementId = $order->getIncrementId();
					
	    	if($customer_id){
	    		
	    		Mage::helper('storecredit')->checkAndInsertCustomerId($customer_id);	
				$_customer = Mage::getModel('storecredit/customer')->load($customer_id);
				$credit = $order->getMhStorecredit();
				$mh_credit_buy = $order->getMhStorecreditBuyCredit();

				if($credit > 0)
            	{
	            	$_customer->addCredit(-$credit);
	            	$transaction_detail = Mage::helper('storecredit')->__('Use to checkout order #%s',$order->getIncrementId());
	           		$historyData = array('customer_id'=>$_customer->getId(),
			    					     'transaction_type'=>Magehit_Storecredit_Model_Type::USE_TO_CHECKOUT, 
								    	 'amount'=>$credit,
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
	    		if($mh_credit_buy > 0)
            	{
            		$transaction_detail = Mage::helper('storecredit')->__('You buy credit in order #%s',$order->getIncrementId());
	           		$historyData = array('customer_id'=>$_customer->getId(),
			    					     'transaction_type'=>Magehit_Storecredit_Model_Type::BUY_CREDIT, 
								    	 'amount'=>$mh_credit_buy,
								    	 'balance'=>$_customer->getCreditBalance(), 
								    	 'transaction_params'=>$order_incrementId,
			    	                     'transaction_detail'=>$transaction_detail,
			    	                     'order_id'=>$order_id,
								    	 'transaction_time'=>now(), 
		    							 'expired_time'=>null,
		            					 'remaining_credit'=>0,
								    	 'status'=>Magehit_Storecredit_Model_Statushistory::PENDING);
	    	
    				Mage::getModel('storecredit/history')->setData($historyData)->save();
					
            	}
	    	}
		}
    }
    
    // canceled payment
 	public function cancelOrder($arvgs)
    {
    	$order = $arvgs->getOrder();
    	$order_id = $order->getId();
    	$order_incrementId = $order->getIncrementId();
    	$customer_id = $order->getCustomerId();
    	$store_id = Mage::getModel('customer/customer') ->load($customer_id)->getStoreId();
    	$_customer = Mage::getModel('storecredit/customer')->load($customer_id);
    	
    	
    	$_transactions = Mage::getModel('storecredit/history')->getCollection()
									->addFieldToFilter('customer_id',$customer_id)
									//->addFieldToFilter('order_id',$order_id) 
									->addFieldToFilter('transaction_params',$order_incrementId)
									->addOrder('transaction_time','ASC')
									->addOrder('history_id','ASC');
		
		foreach($_transactions as $_transaction)
		{
			switch($_transaction->getTransactionType())
			{
				case Magehit_Storecredit_Model_Type::USE_TO_CHECKOUT:
					
					if($_transaction->getTransactionParams() != $order_incrementId) continue;
					
					$transactions_refund = Mage::getModel('storecredit/history')->getCollection()
											->addFieldToFilter('customer_id',$customer_id)
											->addFieldToFilter('order_id',$order_id)
											->addFieldToFilter('transaction_type',Magehit_Storecredit_Model_Type::ORDER_CANCELLED_ADD_CREDIT);
											
					if(sizeof($transactions_refund) >0) continue;
					
					$_customer->addCredit($_transaction->getAmount());	
					$transaction_detail = Mage::helper('storecredit')->__('Restore spent credit for cancelled order #%s',$order->getIncrementId());
					$historyData = array('customer_id'=>$_customer->getId(),
			    					     'transaction_type'=>Magehit_Storecredit_Model_Type::ORDER_CANCELLED_ADD_CREDIT, 
								    	 'amount'=>$_transaction->getAmount(),
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
					break;
					
				case Magehit_Storecredit_Model_Type::BUY_CREDIT:
					
					if($_transaction->getTransactionParams() != $order_incrementId) continue;
					
					$status = Magehit_Storecredit_Model_Statushistory::CANCELLED;
					$_transaction->setTransactionTime(now())->setStatus($status)->save();
					break;
			}
		}
    }
    public function orderSaveAfter($arvgs)
    {
    	$order = $arvgs->getOrder();
    	$store_id = $order->getStoreId();
	  
    	if($order->getStatus() == 'canceled')
		{
			$this ->cancelOrder($arvgs);
		}
    	if($order->getStatus() == 'complete')
		{
			$this ->completeOrder($arvgs);
		}
	
		if($order->getStatus() == 'closed')
		{
			$this ->refundOrder($arvgs);	
		}
    }
	public function completeOrder($arvgs)
    {
    	$order = $arvgs->getOrder();
    	$order_id = $order->getId();
    	$order_incrementId = $order->getIncrementId();
    	$customer_id = $order->getCustomerId();
    	$store_id = Mage::getModel('customer/customer') ->load($customer_id)->getStoreId();
    	$_customer = Mage::getModel('storecredit/customer')->load($customer_id);
    	
    	
    	$_transactions = Mage::getModel('storecredit/history')->getCollection()
									->addFieldToFilter('customer_id',$customer_id)
									//->addFieldToFilter('order_id',$order_id)
									->addFieldToFilter('transaction_params',$order_incrementId)  
									->addOrder('transaction_time','ASC')
									->addOrder('history_id','ASC');
		
		foreach($_transactions as $_transaction)
		{
			switch($_transaction->getTransactionType())
			{
				case Magehit_Storecredit_Model_Type::USE_TO_CHECKOUT:
					
					if($_transaction->getTransactionParams() != $order_incrementId) continue;
					$_transaction->setTransactionTime(now())->setBalance($_customer->getCreditBalance())->save();
					break;
					
				case Magehit_Storecredit_Model_Type::BUY_CREDIT:
					
					$status = Magehit_Storecredit_Model_Statushistory::COMPLETE;
					if($_transaction->getStatus() == $status) continue;
					if($_transaction->getTransactionParams() != $order_incrementId) continue;
					
					$_customer->addCredit($_transaction->getAmount());	
					$_transaction->setTransactionTime(now())->setBalance($_customer->getCreditBalance())->setStatus($status)->save();
					
					$historyData = array('customer_id'=>$_customer->getId(),
			    					     'transaction_type'=>Magehit_Storecredit_Model_Type::BUY_CREDIT, 
								    	 'amount'=>$_transaction->getAmount(),
								    	 'balance'=>$_customer->getCreditBalance(), 
								    	 'transaction_params'=>$order_incrementId,
			    	                     'transaction_detail'=>$_transaction->getTransactionDetail(),
			    	                     'order_id'=>$order_id,
								    	 'transaction_time'=>now(), 
		    							 'expired_time'=>null,
		            					 'remaining_credit'=>0,
								    	 'status'=>Magehit_Storecredit_Model_Statushistory::COMPLETE);
    
    				Mage::helper('storecredit')->sendEmailCustomerCreditChanged($_customer->getId(),$historyData, $store_id);
					break;
			}
		}
					
    }
    public function refundOrder($arvgs)
    {
    	$order = $arvgs->getOrder();
    	$order_id = $order->getId();
    	$order_incrementId = $order->getIncrementId();
    	$customer_id = $order->getCustomerId();
    	$store_id = Mage::getModel('customer/customer') ->load($customer_id)->getStoreId();
    	$_customer = Mage::getModel('storecredit/customer')->load($customer_id);
    	
    	$_transactions = Mage::getModel('storecredit/history')->getCollection()
									->addFieldToFilter('customer_id',$customer_id)
									//->addFieldToFilter('order_id',$order_id)
									->addFieldToFilter('transaction_params',$order_incrementId) 
									->addOrder('transaction_time','ASC')
									->addOrder('history_id','ASC');
		
		foreach($_transactions as $_transaction)
		{
			switch($_transaction->getTransactionType())
			{
				case Magehit_Storecredit_Model_Type::USE_TO_CHECKOUT:
					
					$transactions_refund = Mage::getModel('storecredit/history')->getCollection()
											->addFieldToFilter('customer_id',$customer_id)
											->addFieldToFilter('order_id',$order_id)
											->addFieldToFilter('transaction_type',Magehit_Storecredit_Model_Type::REFUND_ORDER_ADD_CREDIT_CHECKOUT);
											
					if(sizeof($transactions_refund) >0) continue;
					
					if($_transaction->getTransactionParams() != $order_incrementId) continue;
					
					$_customer->addCredit($_transaction->getAmount());	
					$transaction_detail = Mage::helper('storecredit')->__('Restore spent credit for refunded order #%s',$order->getIncrementId());
					$historyData = array('customer_id'=>$_customer->getId(),
			    					     'transaction_type'=>Magehit_Storecredit_Model_Type::REFUND_ORDER_ADD_CREDIT_CHECKOUT, 
								    	 'amount'=>$_transaction->getAmount(),
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
					break;
					
				
				case Magehit_Storecredit_Model_Type::BUY_CREDIT:
					
					$status_complete = Magehit_Storecredit_Model_Statushistory::COMPLETE;
					
					$transactions_refund = Mage::getModel('storecredit/history')->getCollection()
											->addFieldToFilter('customer_id',$customer_id)
											->addFieldToFilter('order_id',$order_id)
											->addFieldToFilter('transaction_type',Magehit_Storecredit_Model_Type::REFUND_ORDER_SUBTRACT_CREDIT);
											
					if(sizeof($transactions_refund) >0) continue;
					
					if($_transaction->getTransactionParams() != $order_incrementId || $_transaction->getStatus() != $status_complete) continue;
					
					$_customer->addCredit(-$_transaction->getAmount());	
					$transaction_detail = Mage::helper('storecredit')->__('Subtract earn credit for refunded order #%s',$order->getIncrementId());
					$historyData = array('customer_id'=>$_customer->getId(),
			    					     'transaction_type'=>Magehit_Storecredit_Model_Type::REFUND_ORDER_SUBTRACT_CREDIT, 
								    	 'amount'=>$_transaction->getAmount(),
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
    				
					break;
			}
		}
    }
}