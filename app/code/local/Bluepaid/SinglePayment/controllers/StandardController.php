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
		 
		 $this->log('URL => '.$this->get_BaseUrl());	
		 $this->log('URL retour stop => '.$this->get_BaseUrl().$this->getvalue('cancelurl'));	
		 $this->log('URL retour ok => '.$this->get_BaseUrl().$this->getvalue('redirecturl'));	
		 $this->log('URL confirmation => '.$this->get_BaseUrl().$this->getvalue('confirmurl').'?'.$token);	
		 $this->log('url token => '.$token);
		 $this->log('Session token => '.$this->_getApiToken());
		 
		 
		 $this->log('Id commerçant => '.$this->getModel()->getConfigData('merchantkey'));	 
		 $this->log('Display form and javascript');
		 
		 $response  = ' <center>
<strong>Please select payment method:</strong>
<p></p>
<form method="post" action="https://www.payssion.com/payment/create.html" id="payment_form">
    <input type="hidden" value="32b9a9f920a8d692" id="api_key" name="api_key">
    <input type="hidden" value="9de5118359ed2e4c9074ae00fef99684" id="api_sig" name="api_sig">
	 <select onchange="change_sig();" id="pm_id" name="pm_id">
		<option value="sofort">Sofort Banking</option>
		<option value="paysafecard">Paysafecard</option>
	</select> 
    <input type="hidden" value="' . $order_id . '" id="track_id" name="track_id">
	<input type="hidden" value="' . $order_id . '" id="sub_track_id" name="sub_track_id">
    <input type="hidden" value="Order # ' . $order_id . '" name="description">
    <input type="hidden" value="'. $order->getBaseTotalDue() . '" id="amount" name="amount">
    <input type="hidden" value="USD" name="currency">
    <input type="hidden" value="en" name="language">
    <input type="hidden" value="http://keyinstant.com/" name="notify_url">
    <input type="hidden" value="http://keyinstant.com/success" name="success_url">
    <input type="hidden" value="http://keyinstant.com/cancel" name="fail_url">
    <input type="submit" value="    Pay    ">
</form>
</center><script language="javascript">
change_sig();
function change_sig(){
var MD5=function(s){function L(k,d){return(k<<d)|(k>>>(32-d))}function K(G,k){var I,d,F,H,x;F=(G&2147483648);H=(k&2147483648);I=(G&1073741824);d=(k&1073741824);x=(G&1073741823)+(k&1073741823);if(I&d){return(x^2147483648^F^H)}if(I|d){if(x&1073741824){return(x^3221225472^F^H)}else{return(x^1073741824^F^H)}}else{return(x^F^H)}}function r(d,F,k){return(d&F)|((~d)&k)}function q(d,F,k){return(d&k)|(F&(~k))}function p(d,F,k){return(d^F^k)}function n(d,F,k){return(F^(d|(~k)))}function u(G,F,aa,Z,k,H,I){G=K(G,K(K(r(F,aa,Z),k),I));return K(L(G,H),F)}function f(G,F,aa,Z,k,H,I){G=K(G,K(K(q(F,aa,Z),k),I));return K(L(G,H),F)}function D(G,F,aa,Z,k,H,I){G=K(G,K(K(p(F,aa,Z),k),I));return K(L(G,H),F)}function t(G,F,aa,Z,k,H,I){G=K(G,K(K(n(F,aa,Z),k),I));return K(L(G,H),F)}function e(G){var Z;var F=G.length;var x=F+8;var k=(x-(x%64))/64;var I=(k+1)*16;var aa=Array(I-1);var d=0;var H=0;while(H<F){Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=(aa[Z]| (G.charCodeAt(H)<<d));H++}Z=(H-(H%4))/4;d=(H%4)*8;aa[Z]=aa[Z]|(128<<d);aa[I-2]=F<<3;aa[I-1]=F>>>29;return aa}function B(x){var k="",F="",G,d;for(d=0;d<=3;d++){G=(x>>>(d*8))&255;F="0"+G.toString(16);k=k+F.substr(F.length-2,2)}return k}function J(k){k=k.replace(/rn/g,"n");var d="";for(var F=0;F<k.length;F++){var x=k.charCodeAt(F);if(x<128){d+=String.fromCharCode(x)}else{if((x>127)&&(x<2048)){d+=String.fromCharCode((x>>6)|192);d+=String.fromCharCode((x&63)|128)}else{d+=String.fromCharCode((x>>12)|224);d+=String.fromCharCode(((x>>6)&63)|128);d+=String.fromCharCode((x&63)|128)}}}return d}var C=Array();var P,h,E,v,g,Y,X,W,V;var S=7,Q=12,N=17,M=22;var A=5,z=9,y=14,w=20;var o=4,m=11,l=16,j=23;var U=6,T=10,R=15,O=21;s=J(s);C=e(s);Y=1732584193;X=4023233417;W=2562383102;V=271733878;for(P=0;P<C.length;P+=16){h=Y;E=X;v=W;g=V;Y=u(Y,X,W,V,C[P+0],S,3614090360);V=u(V,Y,X,W,C[P+1],Q,3905402710);W=u(W,V,Y,X,C[P+2],N,606105819);X=u(X,W,V,Y,C[P+3],M,3250441966);Y=u(Y,X,W,V,C[P+4],S,4118548399);V=u(V,Y,X,W,C[P+5],Q,1200080426);W=u(W,V,Y,X,C[P+6],N,2821735955);X=u(X,W,V,Y,C[P+7],M,4249261313);Y=u(Y,X,W,V,C[P+8],S,1770035416);V=u(V,Y,X,W,C[P+9],Q,2336552879);W=u(W,V,Y,X,C[P+10],N,4294925233);X=u(X,W,V,Y,C[P+11],M,2304563134);Y=u(Y,X,W,V,C[P+12],S,1804603682);V=u(V,Y,X,W,C[P+13],Q,4254626195);W=u(W,V,Y,X,C[P+14],N,2792965006);X=u(X,W,V,Y,C[P+15],M,1236535329);Y=f(Y,X,W,V,C[P+1],A,4129170786);V=f(V,Y,X,W,C[P+6],z,3225465664);W=f(W,V,Y,X,C[P+11],y,643717713);X=f(X,W,V,Y,C[P+0],w,3921069994);Y=f(Y,X,W,V,C[P+5],A,3593408605);V=f(V,Y,X,W,C[P+10],z,38016083);W=f(W,V,Y,X,C[P+15],y,3634488961);X=f(X,W,V,Y,C[P+4],w,3889429448);Y=f(Y,X,W,V,C[P+9],A,568446438);V=f(V,Y,X,W,C[P+14],z,3275163606);W=f(W,V,Y,X,C[P+3],y,4107603335);X=f(X,W,V,Y,C[P+8],w,1163531501);Y=f(Y,X,W,V,C[P+13],A,2850285829);V=f(V,Y,X,W,C[P+2],z,4243563512);W=f(W,V,Y,X,C[P+7],y,1735328473);X=f(X,W,V,Y,C[P+12],w,2368359562);Y=D(Y,X,W,V,C[P+5],o,4294588738);V=D(V,Y,X,W,C[P+8],m,2272392833);W=D(W,V,Y,X,C[P+11],l,1839030562);X=D(X,W,V,Y,C[P+14],j,4259657740);Y=D(Y,X,W,V,C[P+1],o,2763975236);V=D(V,Y,X,W,C[P+4],m,1272893353);W=D(W,V,Y,X,C[P+7],l,4139469664);X=D(X,W,V,Y,C[P+10],j,3200236656);Y=D(Y,X,W,V,C[P+13],o,681279174);V=D(V,Y,X,W,C[P+0],m,3936430074);W=D(W,V,Y,X,C[P+3],l,3572445317);X=D(X,W,V,Y,C[P+6],j,76029189);Y=D(Y,X,W,V,C[P+9],o,3654602809);V=D(V,Y,X,W,C[P+12],m,3873151461);W=D(W,V,Y,X,C[P+15],l,530742520);X=D(X,W,V,Y,C[P+2],j,3299628645);Y=t(Y,X,W,V,C[P+0],U,4096336452);V=t(V,Y,X,W,C[P+7],T,1126891415);W=t(W,V,Y,X,C[P+14],R,2878612391);X=t(X,W,V,Y,C[P+5],O,4237533241);Y=t(Y,X,W,V,C[P+12],U,1700485571);V=t(V,Y,X,W,C[P+3],T,2399980690);W=t(W,V,Y,X,C[P+10],R,4293915773);X=t(X,W,V,Y,C[P+1],O,2240044497);Y=t(Y,X,W,V,C[P+8],U,1873313359);V=t(V,Y,X,W,C[P+15],T,4264355552);W=t(W,V,Y,X,C[P+6],R,2734768916);X=t(X,W,V,Y,C[P+13],O,1309151649);Y=t(Y,X,W,V,C[P+4],U,4149444226);V=t(V,Y,X,W,C[P+11],T,3174756917);W=t(W,V,Y,X,C[P+2],R,718787259);X=t(X,W,V,Y,C[P+9],O,3951481745);Y=K(Y,h);X=K(X,E);W=K(W,v);V=K(V,g)}var i=B(Y)+B(X)+B(W)+B(V);return i.toLowerCase()};
document.getElementById("api_sig").value = MD5("32b9a9f920a8d692" + "|" + document.getElementById("pm_id").value + "|" + document.getElementById("amount").value + "|" + "USD" + "|" + document.getElementById("track_id").value  + "|" + document.getElementById("sub_track_id").value + "|" + "9de5118359ed2e4c9074ae00fef99684");
}
</script>';


		 $this->getResponse()->setBody($response);

		
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