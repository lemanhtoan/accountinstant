<?php
#####################################################################################################
#
#					Module pour la plateforme de paiement Bluepaid
#						Version : 0.1 
#									########################
#					Développé pour Magento
#						Version : 1.7.0.X
#						Compatibilité plateforme : V2
#									########################
#					Développé par Bluepaid
#						http://www.bluepaid.com/
#						22/02/2013
#						Contact : support@bluepaid.com
#
#####################################################################################################


class Bluepaid_SinglePayment_StandardController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */	
	 public function paymentAction() {
		 $this->log('Start'); 

    	// Load session
    	$session = Mage::getSingleton('checkout/session');
    	$session->setBluepaidStandardQuoteId($session->getQuoteId());

    	// Clear redirect url : it is not useful anymore and may be called from unwanted locations (e.g. cart/add with out of stock products...)
    	$session->unsRedirectUrl();

		// Load order
    	$order_id = $session->getLastRealOrderId();
    	/* @var $order Mage_Sales_Model_Order */
    	$order = Mage::getModel('sales/order');
		$order->loadByIncrementId($order_id);

		// Check No risk
		if(!$order->getId()) {
			$this->log("Payment attempt for an unknown order id - interrupting process.", Zend_Log::WARN);
			Mage::throwException($this->__("No order for processing found"));
			return;
		}    	
		// Check No risk 2 : check vs. the client who had no cart...
    	if ($order->getTotalDue() == 0) {
    		$this->log("Payment attempt with no amount - redirecting to cart.");
            $this->_redirect('checkout/cart', array('_store'=>$order->getStore()->getId()));
            return;
        }
    	// ------------
    	// Display form
    	// ------------
    	// Set config parameters
    	$this->log('Initialize payment parameters');		
        $quoteId = $session->getQuoteId();
        $session->setData('bluepaidQuoteId',$quoteId);
        
        $quote = Mage::getModel("sales/quote")->load($quoteId);
        $grandTotal = $quote->getData('grand_total');
				
		 $this->log('Montant => '.$grandTotal);	
		 $grandTotal = $this->_getFormatedAmount($grandTotal);
		 $this->log('Montant formaté => '.$grandTotal);	
		 		 
		 //GET token will be sent in GET method when returning customer
         $api = new Bluepaid_SinglePayment_Model_Api();
		 $api->_setApiToken();
		 $token=$api->_getTokenApi();
		 
		 $email = $order->getBillingAddress()->getEmail();
		 if (!$email) $email = $order->getCustomerEmail();
		 
		 //$this->log('Id commerçant => '.$this->getModel()->getConfigData('merchantkey'));	 
		 $accesskey = $this->getModel()->getConfigData('accesskey');
		 $profileid = $this->getModel()->getConfigData('profileid');
		 $secretkey = $this->getModel()->getConfigData('secretkey');
		 $submiturl = $this->getModel()->getConfigData('submiturl');
		 
		 $firstname ='';		
		 $lastname ='';
		 $country ='';
		 $phone='';
		 
		 //If they have no customer id, they're a guest.
		 if($order->getCustomerId() === NULL){
			$firstname = $order->getBillingAddress()->getFirstname();
			$lastname = $order->getBillingAddress()->getLastname();
			$phone = $order->getBillingAddress()->getTelephone();
			$country = $order->getBillingAddress()->getCountry();
		 } else {
		//else, they're a normal registered user.
			$customer = Mage::getModel('customer/customer')->load($order->getCustomerId());
			try{
			//$firstname = $customer->getDefaultBillingAddress()->getFirstname();
			//$lastname = $customer->getDefaultBillingAddress()->getLastname();
			//$phone = $customer->getDefaultBillingAddress()->getTelephone();
			//$country = $customer->getDefaultBillingAddress()->getCountry();
			$firstname = $order->getBillingAddress()->getFirstname();
			$lastname = $order->getBillingAddress()->getLastname();
			$phone = $order->getBillingAddress()->getTelephone();
			$country = $order->getBillingAddress()->getCountry();
			}catch (Exception $exec){}
		 } 
		 
		 $params = array(
			"access_key" =>  $accesskey,
			"profile_id" => $profileid,
			"transaction_uuid" => uniqid(),
			"signed_field_names" => "access_key,profile_id,transaction_uuid,signed_field_names,unsigned_field_names,signed_date_time,locale,transaction_type,reference_number,amount,currency,bill_to_email,bill_to_forename,bill_to_surname,bill_to_address_country",
			"unsigned_field_names" => "",
			"signed_date_time" => gmdate("Y-m-d\TH:i:s\Z"),
			"locale" => "en",
			"transaction_type" => "sale",
			"reference_number" => $order_id,
			"currency" => "USD",
			"amount" => $order->getBaseTotalDue(),
			"bill_to_email" => $email,
			"bill_to_forename" => $firstname,
			"bill_to_surname" => $lastname,
			"bill_to_address_country" => $country,
			"bill_to_phone" => $phone,
		);
		
		$params["signature"] = $this->signData($this->buildDataToSign($params), $secretkey);
		
		$response = '<center>Your payment is processing</center><form id="payment_confirmation" action="'.$submiturl.'" method="post"/>';
		foreach($params as $name => $value) {
			$response = $response.' '. "<input type=\"hidden\" id=\"" . $name . "\" name=\"" . $name . "\" value=\"" . $value . "\"/>\n";
		}
		$response = $response.' </form><script type="text/javascript">
		 window.onload=function(){
                    document.forms[0].submit();
                }
		</script>';
		
		$this->getResponse()->setBody($response);		
	 }
	
	public function signData($data, $secretKey) {
		return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
	}

	public function buildDataToSign($params) {
        $signedFieldNames = explode(",",$params["signed_field_names"]);
        foreach ($signedFieldNames as $field) {
           $dataToSign[] = $field . "=" . $params[$field];
        }
        return implode(",",$dataToSign);
	}
	
    /**
	*****************
	*****************
    * ACTIONS ON ORDER
	*****************
	*****************
    */
    //RETOUR STOP => NO VALIDATION OF PAYMENT PAGE
    public function cancelAction(){
    	$this->log('Start');
    	$this->log('Called ' . __METHOD__); 
		$orderId = $this->getRequest()->getParam('id_client');
		##		
    	$this->log('Start');  
    	$this->log('Transaction => '.$orderId);     	
    	$this->log('Retrieving statuses configuration');
		##
		$order = Mage::getModel('sales/order');
		$order->load($orderId);		
		$this->refillCart($order);
        $this->_redirect('checkout/cart');
    }
    
    /**
	/**SUCESS ONLY ON BLUEPAID CONFIRMATION
     */
    public function  successAction()
    {		
		//////////
		////TODO
		//////////
        /*  if(!$this->_isValidToken()){
    		$this->log('Token is invalid.');
            $this->_redirect('checkout/cart');    
        }*/
		if($_SERVER['REQUEST_METHOD']=='POST'){
       		$api = new Bluepaid_SinglePayment_Model_Api();
			if($api->Is_authorizedIp($_SERVER['REMOTE_ADDR'])){
				$this->log('Access to '.__METHOD__.' with method POST');
				$orderId = $this->getRequest()->getParam('id_client');	
				
				$this->log('Validate order ' . $orderId);
				if ($orderId) {					
					$statusPayment = $this->getRequest()->getParam('etat');
					$statusPayment = strtolower($statusPayment);
					if($statusPayment == 'ok' || $statusPayment == 'ko'){				
						//MODE TEST			
						$order = Mage::getModel('sales/order');
						$order->load($orderId);			
					
						$this->log('Mise a jour pour la commande '.$order->getId());		
						$this->log('Mise a jour du paiement en attente');  
						$this->log('Statut du paiement pour mise a jour '. $statusPayment); 
						switch($statusPayment){					
							case 'ok':
								$this->log('Envoi a registerOrder'); 
								$this->registerOrder($order, $this->getRequest());
							break;
							case 'ko':
								$this->log('Envoi a manageRefusedPayment'); 
								$this->manageRefusedPayment($order);
							break;
						}
					}
				}
			}else{
				$this->log('Trying to acces '.__METHOD__.' with unauthorized IP => '.$_SERVER['REMOTE_ADDR']);
			}
		}else{
			$this->log('Trying to acces '.__METHOD__.' with method GET');
        	$this->_redirect('');
		}
    }
	    /**
     *  Save invoice for order => payment was accepted
     *
     *  @param    Mage_Sales_Model_Order $order, parals of trans
     *  @return	  boolean Can save invoice or not
     */
    protected function registerOrder(Mage_Sales_Model_Order $order, $params_trs="") {
		$msg_user='Payment made with bluepaid<br />';
		##		
    	$this->log('Start');  
    	$this->log('Transaction => '.$params_trs->getParam('id_trans'));     	
    	$this->log('Retrieving statuses configuration');
		##
				
    	$newStatus = $this->getModel()->getConfigData('registered_order_status', $order->getStore()->getId());
    	$stateStatusInfo = Mage::getModel('sales/order_config');
    	$processingStatuses = $stateStatusInfo->getStateStatuses(Mage_Sales_Model_Order::STATE_PROCESSING);
			
		##	
		$msg = 'Trying to modify status of payment to '. $newStatus.'.';
		$this->log($msg);
		
		$transaction_test=false;
		if($params_trs->getParam('mode')=='test'){
			$transaction_test=true;
			$msg_user.='<b>CAUTION !! IT IS A TRANSACTION IN TEST MODE (FROM BLUEPAID)</b><br />';
		}
		
        $api = new Bluepaid_SinglePayment_Model_Api();
		$account_in_test=false;	 $istest_mode='production';	
		/*if($api->Is_testMode()){
			$istest_mode='test';	
			$account_in_test=true;
			$msg_user.='<b>YOUR ACCOUNT IS IN TEST MODE</b><br />';
		}*/
		$msg = 'Account is actually in mode '. $istest_mode.'.';
		$this->log($msg);
		##	
		$msg_user.='Transaction '.$params_trs->getParam('id_trans');
			
		if(($account_in_test&&$transaction_test) || (!$account_in_test&&!$transaction_test)){
			if (array_key_exists($newStatus, $processingStatuses)) {
				$this->log('Capturing payment for order '.$order->getId());			
				if($order->canInvoice()) {
					$this->log('Creating invoice for order '.$order->getId());				
					$msg = 'Payment completed via Bluepaid with invoice.';	
					$this->log($msg);	
							
					$msg_user.='<br />';
					$msg_user.='Completed with an invoice (if available)';
					$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $msg_user)->save();
					$msg = 'Update ok.';	
					$this->log($msg);	
					/***A TERMINER => GENERATION DE FACTURE***/		
				/*	$invoice = $order->prepareInvoice();
					$invoice->register()->capture();				
					Mage::getModel('core/resource_transaction')
					  ->addObject($invoice)
					  ->addObject($invoice->getOrder())
					  ->save();				
					
					$msg = 'Payment completed via Bluepaid with invoice.';	
					$this->log($msg);		
					$order->setState(Mage_Sales_Model_Order::STATE_COMPLETE, true, $msg);	*/
					
				} else {			
					$msg = 'Payment completed via Bluepaid without invoice.';	
					$this->log($msg);
					$msg_user.='<br />';
					$msg_user.='Completed without invoice';
					$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, $msg_user)->save();
					$msg = 'Update ok.';	
					$this->log($msg);	
				}
			} else {
				$this->log('Capturing payment for order '.$order->getId());
				$msg = 'Payment captured via Bluepaid.';	
				$this->log($msg);	
				$msg_user.='<br />';
				$msg_user.='Captured but no new status was available for this order';
				$order->setState(Mage_Sales_Model_Order::STATE_NEW, true, $msg_user)->save();
				$msg = 'Update ok.';	
				$this->log($msg);			
			}
	
			$this->log('Saving confirmed order and sending email');
			$order->sendNewOrderEmail();
			$this->log('End');
		}else{
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, $msg_user)->save();
		}
    }
	    /*  Cancel operation (return to web site FOMR bluepaid)
     *
     *  @param    //
     *  @return	  boolean 
     */
    protected function _cancelAction()
    {
    	$this->log('Start');
    	$this->log('Called ' . __METHOD__);
        if(!$this->_isValidToken()){
    		$this->log('Token is invalid.');
            $this->_redirect('checkout/cart');    
        }
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($this->_getApiQuoteId());
         /* @var $quote Mage_Sales_Model_Quote */
        $quote = $session->getQuote();
        $quote->setIsActive(false)->save();
        $quote->delete();
        
        $orderId = $this->_getApiOrderId();
		$this->log('Canceling order ' . $orderId);
        if ($orderId) {
            $order = Mage::getSingleton('sales/order');
            $order->load($orderId);
            if ($order->getId()) {
                $state = $order->getState();
				$this->log('Prepare to cancel.');
                if($state == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT){
                    $order->cancel()->save();
					$this->log('Your order has been canceled.');
                    Mage::getSingleton('core/session')->addNotice('Your order has been canceled.');
                }
            }
        }
        $this->_redirect('checkout/cart');
    	$this->log('End');
    }
    /**
     * Rejected payment by bluepaid
     * @param Mage_Sales_Model_Order $order
     */
    protected function manageRefusedPayment(Mage_Sales_Model_Order $order) {
    	$this->log('Start');
    	$this->log('Canceling order '.$order->getId(), Zend_Log::INFO);
    	$order->cancel()->save();

    	/* @var $session Mage_Checkout_Model_Session */
    	$this->log('Unsetting order data in session');
    	$session = Mage::getSingleton('checkout/session');
    	$session->unsLastQuoteId()
    			->unsLastSuccessQuoteId()
    			->unsLastOrderId()
    			->unsLastRealOrderId();
				
    	$this->refillCart($order);
		
		$this->log('End');
    }
	
    /**
     * Refill the cart
     * @param Mage_Sales_Model_Order $order
     */
	function refillCart(Mage_Sales_Model_Order $order){    	
    	if($this->getModel()->getConfigData('refill_bpcart', $order->getStore()->getId())) {
			// Re-fill the cart so that the client can reorder quicker
			$cart = Mage::getSingleton('checkout/cart');
			$items = $order->getItemsCollection();
			foreach ($items as $item) {
	            try {
	                $cart->addOrderItem($item,$item->getQty());
	            } catch (Mage_Core_Exception $e){
	                if (Mage::getSingleton('checkout/session')->getUseNotice(true)) {
	                    Mage::getSingleton('checkout/session')->addNotice($e->getMessage());
	                } else {
	                    Mage::getSingleton('checkout/session')->addError($e->getMessage());
	                }
	            } catch (Exception $e) {
	                Mage::getSingleton('checkout/session')->addException($e,
	                    Mage::helper('checkout')->__('Cannot add the item to shopping cart.')
	                );
	            }
	        }
	        
	        // Associate cart with order customer
	        $customer = Mage::getModel('customer/customer');
	        $customer->load($order->getCustomerId());
	        $cart->getQuote()->setCustomer($customer);
	        $cart->save();
    	}
	}
    /**
	*****************
	*
    * END  ACTIONS ON ORDER
	*
	*****************
	*****************
    */	

    protected function _isValidToken(){
    	$this->log('Start');
    	$this->log('Called custom ' . __METHOD__);
        $uriToken = $this->getRequest()->getParam('token');
        $sessionToken = $this->_getApiToken();
        $this->log("Testing tokens(uri/session) $uriToken/$sessionToken");
        if($uriToken == $sessionToken){
            return true;
        }
        return false;
    }
    
    protected function _getApiToken(){
    	$this->log('Start');
    	$this->log('Called custom ' . __METHOD__);
		$sessionToken=$_SESSION["bluepaidApiToken"];
    	$this->log('session token ' . $sessionToken);
        return $sessionToken;
    }
	 
	 function _getFormatedAmount($amount=0){
		return number_format($amount, 2, '.', ''); 
	 }
	 
	 function getvalue($field=''){
		 return $this->getModel()->getConfigData($field);
	 }
	 
	 function get_BaseUrl($no_http=false){
		 $url=Mage::getBaseUrl(Mage_Core_Model_Store:: URL_TYPE_WEB);
		 if($no_http){
			if (preg_match('#https?://([^/]+/)+#', $url)) {
				$url=str_replace('https://', '', $url);
			}
			if (preg_match('#http?://([^/]+/)+#', $url)) {
				$url=str_replace('http://', '', $url);
			}		 	
		 }
		 return $url;
	 }
	 
    protected function _getCheckout()
    {
    	$this->log('Start ');
        return Mage::getSingleton('checkout/session');
    }
    
    protected function _getApiQuoteId(){
    	$this->log('Start');
        $quoteId = Mage::getSingleton('checkout/session')->getData('bluepaidQuoteId');
        $this->log('Returned quoteId ' . $quoteId);
        return $quoteId;
    }
    
    protected  function _getApiOrderId(){
    	$this->log('Start');
        $orderId = Mage::getSingleton('checkout/session')->getData('apiOrderId');
        Mage::log('Returned orderId ' . $orderId);
        return $orderId;
    }
    /**
    * Builds invoice for order
    */
    protected function _createInvoice()
    {
    	$this->log('Start');
        if (!$this->_order->canInvoice()) {
            return;
        }
        $invoice = $this->_order->prepareInvoice();
        $invoice->register()->capture();
        $this->_order->addRelatedObject($invoice);
    }
    /**
     * Handles 'falures' from api
     * Failure could occur if api system failure, insufficent funds, or system error.
     * @throws Exception
     */
    public function failureAction(){
    	$this->log('Start');
        Mage::Log('Called ' . __METHOD__);
        $this->cancelAction();
    }

	/**
	 * Log function. Uses Mage::log with built-in extra data (module version, method called...)
	 * @param $message
	 * @param $level
	 */
    protected function log($message, $level=null) {
    	$currentMethod = $this->getCallerMethod();
    	
		if (!Mage::getStoreConfig('dev/log/active')) {
    		return;
    	}

    	$log  = '';
    	$log .= 'Bluepaid 1.1 37000';
    	$log .= ' - '.$currentMethod;
    	$log .= ' : '.$message;
		Mage::log($log, $level, 'bluepaid.log');
    }
    
	protected function getCallerMethod() {
    	$traces = debug_backtrace();
    
    	if (isset($traces[2])) {
    		return $traces[2]['function'];
    	}
    
    	return null;
    }
    function getModel() {
    	return Mage::getModel('bluepaid/standard');
    }
}