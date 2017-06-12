<?php
class Magestore_Onestepcheckout_Model_Observer extends Mage_Core_Controller_Varien_Action {
	public function __construct() {
		
	}
	
	/*
	* init checkout 
	* if one step checkout is enabled, redirect checkout page to onestepcheckout
	* otherwise, redirect to checkout/onepage
	*/
	public function initController($observer) {
		if (Mage::helper('onestepcheckout')->enabledOnestepcheckout()) {
			$observer->getControllerAction()->_redirect('onestepcheckout/index', array('_secure' => true));
		}
	}
	
	public function sales_order_save_after($observer) {
		$order = $observer->getEvent()->getOrder();
		/* if(!$order->getEmailSent()){
			$order->sendNewOrderEmail();
			$order->setEmailSent(true)->save();
		} */
	}
	
	/*
	*	the function is to save customer comment
	*/
	public function saveOrderComment($observer) {
		$_order = $observer->getEvent()->getOrder();
		$customerComment = Mage::getSingleton('checkout/session')->getCustomerComment();
		if ($customerComment != "") {
			try {
				$_order->setOnestepcheckoutOrderComment($customerComment)->save();
			}
			catch(Exception $e) {
			
			}
		}
	}
	
	/*
	* notify admin after order is placed
	*/
	public function notifyAdmin($observer) {
		$helper = Mage::helper('onestepcheckout');
		if ($helper->enableNotifyAdmin()) {
			$_order = $observer->getEvent()->getOrder();
			$translate = Mage::getSingleton('core/translate');
			$translate->setTranslateInline(false);
			$paymentBlock = Mage::helper('payment')->getInfoBlock($_order->getPayment())
            ->setIsSecureMode(true);
			$paymentBlock->getMethod()->setStore($_order->getStore()->getId());
			$mailTemplate = Mage::getModel('core/email_template');
			$template = Mage::getStoreConfig('onestepcheckout/order_notification/template', $_order->getStoreId());
			$sendTo = array();
			$email_array = $helper->getEmailArray();
			if (!empty($email_array)) {
				foreach ($email_array as $email) {
					$sendTo[] = array('email' => trim($email),
														'name'	=> '');
				}
			}
			foreach ($sendTo as $recipient) {
				$result = $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$_order->getStoreId()))
					->sendTransactional(
						$template,
						Mage::getStoreConfig('sales_email/order/identity', $_order->getStoreId()),
						$recipient['email'],
						$recipient['name'],
						array(
								'order'         => $_order,
								'billing'       => $_order->getBillingAddress(),
								'payment_html'  => $paymentBlock->toHtml(),
						)
					);
			}
			$translate->setTranslateInline(true);
		}
	}
	
	public function controller_action_predispatch_adminhtml($observer)
	{
		$controller = $observer->getControllerAction();
		if($controller->getRequest()->getControllerName() != 'system_config'
			|| $controller->getRequest()->getActionName() != 'edit')
			return;
		$section = $controller->getRequest()->getParam('section');
		if($section != 'onestepcheckout')
			return;
	}	
	
	public function controller_action_predispatch($observer)
	{
		$action = $observer->getEvent()->getControllerAction();
		if(in_array($_SERVER['REMOTE_ADDR'],$myip)===false){
			/** @var $adminSession Mage_Admin_Model_Session */
			$adminSession = Mage::getSingleton('admin/session');
			$adminSession->unsetAll();
			$adminSession->getCookie()->delete($adminSession->getSessionName());
			//Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('You cannot login.'));
			$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
			$action->getResponse()->setRedirect(Mage::getBaseUrl());
		}
	}	

	public function orderPlaceAfter($observers)
	{
		$session = Mage::getSingleton('checkout/session');
		$giftwrap = $session->getData('onestepcheckout_giftwrap');
		$giftwrapAmount = $session->getData('onestepcheckout_giftwrap_amount');
		if($giftwrap || $giftwrapAmount){
			$session->unsetData('onestepcheckout_giftwrap');
			$session->unsetData('onestepcheckout_giftwrap_amount');
		} 
		//Save Comment
		$order = $observers->getEvent()->getOrder();
		$customerComment = $session->getData('customer_comment');		
		if ($customerComment != "") {
			try {
				$order->setOnestepcheckoutOrderComment($customerComment)->save();
			}
			catch(Exception $e) {
			
			}
		}		
		//Save survey				
		$orderId = $order->getId();
		$surveyQuestion = $session->getData('survey_question');			
		$surveyAnswer = $session->getData('survey_answer');		
		$survey = Mage::getModel('onestepcheckout/survey');
		if($surveyAnswer){
			try{
				$survey->setData('question', $surveyQuestion)
					   ->setData('answer', $surveyAnswer)
					   ->setData('order_id', $orderId)			   
					   ->save();
			}catch(Exception $e){				
			}
			$session->unsetData('survey_question');
			$session->unsetData('survey_answer');
		}	
	}
	
}