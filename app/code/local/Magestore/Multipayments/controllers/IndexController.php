<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Multipayments
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Multipayments Index Controller
 * 
 * @category    Magestore
 * @package     Magestore_Multipayments
 * @author      Magestore Developer
 */
class Magestore_Multipayments_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
	
	public function finishAction(){
		$receiptId = "12345678";
		$customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
		if($post = $this->getRequest()->getPost()){
			$object = new Varien_Object($post);
			$orderId = $object->getOrderId();
			$order = Mage::getModel('sales/order')->load($orderId);
			if($object->getLrPaidto() == 'U4785635' && $object->getLrAmnt() == 0.01 && $order->getCustomerId() == $customerId){
				$order->setStatus('complete')
					->save();
			}
			$this->_redirectUrl(Mage::getUrl('sales/order/view',array('order_id'=>$orderId)));
			return;
		}
		$this->_redirectUrl(Mage::getUrl('customer/account/index'));
	}
}