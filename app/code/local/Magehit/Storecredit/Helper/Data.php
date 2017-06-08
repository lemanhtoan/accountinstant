<?php

class Magehit_Storecredit_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MYCONFIG = "storecredit/config/enabled";
	const MYNAME = "Magehit_Storecredit";

	public function myConfig()
    {
    	return self::MYCONFIG;
    }
	public function moduleEnabled()
	{
        $store_id = Mage::app()->getStore()->getId();
		return Mage::getStoreConfig('storecredit/config/enabled',$store_id);
	}
	public function formatCredit($credit,$store_id=null)
	{
		if(is_null($store_id)) $store_id = Mage::app()->getStore()->getId();
		return Mage::helper('core')->currency($credit,false,false).$this->__(' Credit');
	}
	public function getCreditByCustomer()
	{
		$customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		if($customer_id){
			
			$this->checkAndInsertCustomerId($customer_id);
			$customerCredit = Mage::getModel('storecredit/customer')->load($customer_id)->getCreditBalance();
			return Mage::helper('core')->currency($customerCredit,false,false);
		} 
		return 0;
	}
	
	public function getLinkCustomer($customer_id,$detail)
	{   
		$url = "adminhtml/customer/edit";
		$result='';
		$result = $this->__("<b><a href=\"%s\">%s</a></b>",Mage::helper('adminhtml')->getUrl($url,array('id'=>$customer_id)),$detail);
		return $result;
	}
    
    public function getDisplayCreditListProduct($_product)
    {
        $store_id = Mage::app()->getStore()->getId();
        $credit = Mage::getModel('catalog/product')->load($_product->getId())->getMhStorecredit();

        $enable      = $this->moduleEnabled();
        if($credit > 0 && $enable)
        {
            return '<div style="color: #CD5033;font: 13px/1.55 Arial,Helvetica,sans-serif; font-weight: bold;">'.$this->__('Credits awarded: '). $credit.'</div>';
        }

        return '';
    }

    public function displayCreditViewProduct($_product)
    {

        $store_id = Mage::app()->getStore()->getId();
        $credit = Mage::getModel('catalog/product')->load($_product->getId())->getMhStorecredit();

        $enable      = $this->moduleEnabled();
        if($credit > 0 && $enable)
        {
            return '<div style="float:left; width:100%; color: #CD5033;font: 13px/1.55 Arial,Helvetica,sans-serif; font-weight: bold;">'.$this->__('Credits awarded: '). $credit.'</div>';
        }

        return '';
    }
    
	public function getMaxCreditConfig($baseGrandTotal,$store_id)
	{
		$max_config = Mage::getStoreConfig('storecredit/config/max_checkout',$store_id);
		if($max_config == '' || $max_config == 0 ){
			
			return $baseGrandTotal;
			
		}else if(strpos($max_config,'%') == false){
			
			if($max_config > $baseGrandTotal) return $baseGrandTotal;
			return $max_config;
		}else{
			
			$max_array = explode('%',$max_config);
			return round((($max_array[0] * $baseGrandTotal)/100),2);
			
		}
		return $baseGrandTotal;
	}
	
	public function getMinCreditConfig($baseGrandTotal = null,$store_id = null)
	{
		if(is_null($store_id)) $store_id = Mage::app()->getStore()->getId();
		if(is_null($baseGrandTotal)) $baseGrandTotal = Mage::getSingleton('checkout/session')->getQuote()->getBaseGrandTotal();
		$min_config = Mage::getStoreConfig('storecredit/config/min_checkout',$store_id);
		if($min_config == '' || $min_config == 0 ){
			
			return 0;
			
		}else if(strpos($min_config,'%') == false){
			
			return (int)$min_config;
			
		}else{
			
			$min_array = explode('%',$min_config);
			return round((($min_array[0] * $baseGrandTotal)/100),2);
			
		}
		return 0;

	}
	public function checkAndInsertCustomerId($customer_id)
	{
		if($customer_id){
			$collection_customer = Mage::getModel('storecredit/customer')->getCollection()
											->addFieldToFilter('customer_id', $customer_id);
											
			if(sizeof($collection_customer) == 0){
				
				$data = array('customer_id'    =>$customer_id,
				              'credit_balance' =>0,
							  'credit_updated_notification'=>1);
				
				Mage::getModel('storecredit/customer')->setData($data)->setId($customer_id)->save();
			}
		}
	}
	
	public function getMaxCreditToCheckOut($baseGrandTotal = null,$quote = null,$customer_id = null,$store_id = null)
	{
		$tax = 0;
		$shipping = 0;
		if(is_null($store_id)) $store_id = Mage::app()->getStore()->getId();
		if(is_null($customer_id)) $customer_id = Mage::getSingleton('customer/session')->getCustomer()->getId();
		if(is_null($quote)) $quote = Mage::getSingleton('checkout/session')->getQuote();
	
		if ($quote->isVirtual()) {
    		$address = $quote->getBillingAddress();
    	}else
    	{
    		$address = $quote->getShippingAddress();
    	}

		$tax = $address->getBaseTaxAmount();
		$shipping = $address->getBaseShippingInclTax();
			
		if(is_null($baseGrandTotal)) $baseGrandTotal = $quote->getBaseGrandTotal()- $tax - $shipping;
		
		$maxCredit = $this->getMaxCreditConfig($baseGrandTotal,$store_id);
		
		$credit = $baseGrandTotal + $quote->getMhStorecreditDiscount();
		
		if($customer_id){
			$customerCredit = Mage::getModel('storecredit/customer')->load($customer_id)->getCreditBalance();
			
		}else 
			$customerCredit = 0;
		
    	if($maxCredit){
	    	
    		if($customerCredit >= $maxCredit){
				if($maxCredit < $credit) return $maxCredit;
				return $credit;
			}else{
				if($customerCredit < $credit) return $customerCredit;
				return $credit;
			}
    	}
    	return 0;
	}
	public function setCreditToCheckOutAdmin($credit,$quote,$customer_id,$store_id,$baseGrandTotal)
	{
		if(!(Mage::helper('storecredit')->moduleEnabled()))
		{
			return;
		}
		
    	$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode(); 
    	$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();  
    
    	$max_credit = $quote->getMhStorecreditCheckoutMax();
    	$min_credit = $quote->getMhStorecreditCheckoutMin();
    	
       // $credit = abs($credit);
        
        
		if ($credit == 0) {
			$quote->setMhStorecredit(0)->save();
        	$quote ->collectTotals()->save();
        
			Mage::getSingleton('adminhtml/session_quote')->addSuccess(
            	$this->__('The credit has cancelled successfully.')
          	);
          	return;
		}
        
		if ($credit < 0) {
			Mage::getSingleton('adminhtml/session_quote')->addError(
            	$this->__('Your entered amount (%s) is less than 0.', Mage::helper('core')->currency($credit,true,false))
          	);
          	return;
		}
       
		$credit = Mage::helper('directory')->currencyConvert($credit, $currentCurrencyCode, $baseCurrencyCode);
		
		if ($credit > $max_credit) {
			Mage::getSingleton('adminhtml/session_quote')->addError(
            	$this->__('Your entered amount (%s) is greater than max credit.', Mage::helper('core')->currency($credit,true,false))
          	);
          	return;
		}
		
    	if ($credit < $min_credit) {
			Mage::getSingleton('adminhtml/session_quote')->addError(
            	$this->__('Your entered amount (%s) is less than min credit.', Mage::helper('core')->currency($credit,true,false))
          	);
          	return;
		}
        
        $quote->setMhStorecredit($credit)->save();
        $quote ->collectTotals()->save();
        
        if ($credit) {
        	Mage::getSingleton('adminhtml/session_quote')->addSuccess(
            	$this->__('Credit "%s" was applied successfully.', Mage::helper('core')->currency($credit,true,false))
        	);
        } else {
            Mage::getSingleton('adminhtml/session_quote')->addSuccess($this->__('The credit has cancelled successfully.'));
        }

	}
    public function allowSendCreditToFriend($store_id)
    {
        return Mage::getStoreConfig('storecredit/config/send_credit_to_friend',$store_id);
    }
	public function allowSendEmailNotifications($store_id)
	{
		return Mage::getStoreConfig('storecredit/email_notifications/enable_notifications',$store_id);
	}
	public function sendEmailCustomerCreditChanged($customer_id,$data, $store_id)
   	{ 
   		$_customer = Mage::getModel('storecredit/customer')->load($customer_id);
		$credit_updated_notification = $_customer->getCreditUpdatedNotification();
		
   		if($this->allowSendEmailNotifications($store_id) &&  $credit_updated_notification == 1)
   		{
	   		$store_name = Mage::getStoreConfig('general/store_information/name', $store_id);
	    	$sender = Mage::getStoreConfig('storecredit/email_notifications/email_sender', $store_id);
	    	$email = Mage::getModel('customer/customer')->load($customer_id)->getEmail();
	    	$name = Mage::getModel('customer/customer')->load($customer_id)->getName();
	    	$teampale = 'storecredit/email_notifications/credit_balance';
	    	$sender_name = Mage::getStoreConfig('trans_email/ident_'.$sender.'/name', $store_id);
	    	$customer_link = Mage::getBaseUrl();
	    	$data_mail['customer_name'] = $name;
	    	$data_mail['transaction_amount'] = Magehit_Storecredit_Model_Type::getAmountWithSign($data['amount'],$data['transaction_type']);
	    	$data_mail['customer_balance'] = $data['balance'];
	    	$data_mail['transaction_detail'] = $data['transaction_detail'];
	    	$data_mail['transaction_time'] = Mage::helper('core')->formatDate($data['transaction_time'])." ".Mage::helper('core')->formatTime($data['transaction_time']);
	    	$data_mail['sender_name'] = $sender_name;
	    	$data_mail['store_name'] = $store_name;
	    	$data_mail['customer_link'] = $customer_link;
	    	$this ->_sendEmailTransaction($sender,$email,$name,$teampale,$data_mail,$store_id);
   		}
   	}
	
	public function _sendEmailTransaction($sender, $emailto, $name, $template, $data, $store_id)
   	{   
   		  $data['subject'] = 'Storecredit System !'; 
   		  $templateId = Mage::getStoreConfig($template,$store_id);
		  $translate  = Mage::getSingleton('core/translate');
		  $translate->setTranslateInline(false);

		  try{
			  Mage::getModel('core/email_template')
			      ->sendTransactional(
			      $templateId, 
			      $sender, 
			      $emailto, 
			      $name, 
			      $data, 
			      $store_id);
			  $translate->setTranslateInline(true);
		  }catch(Exception $e){
		  		$this->_getSession()->addError($this->__("Email can not send !"));
		  }
   	}
	
	function disableConfig()
	{
			Mage::getSingleton('core/config')->saveConfig($this->myConfig(),0); 			
			Mage::getModel('core/config')->saveConfig("advanced/modules_disable_output/".self::MYNAME,1);	
			 Mage::getConfig()->reinit();
	}

	public function checkInsertCustomerIdStoreCredit($customer_id, $credit_updated_notification)
	{
		if($customer_id){
			$collection_customer = Mage::getModel('storecredit/customer')->getCollection()
											->addFieldToFilter('customer_id', $customer_id);
			if(sizeof($collection_customer) == 0){
				$_customer_table = Mage::getModel('storecredit/customer')->getCollection();
				$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		        $sql = 'INSERT INTO '.$_customer_table->getTable('customer').'(customer_id,credit_balance,credit_updated_notification) VALUES('.$customer_id.',0,'. (($credit_updated_notification)?$credit_updated_notification:0).')';
		        $write->query($sql);
			}
		}
	}
}