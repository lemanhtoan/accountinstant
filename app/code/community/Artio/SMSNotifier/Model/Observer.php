<?php
/**
 * SMS Notifier
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Artio
 * @package     Artio_SMSNotifier
 * @copyright   Copyright (c) 2013 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * SMS Model
 *
 * @category Artio
 * @package Artio_SMSNotifier
 * @author Artio Magento Team <info@artio.net>
 */
class Artio_SMSNotifier_Model_Observer
{

	/**
	 *
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Artio_SMSNotifier_Model_Observer
	 */
	public function beforeSaveAbstract($observer)
	{
		try
		{
			$event  = $observer->getEvent();
			$object = $event->getData('object');

			if (
				    ($object instanceof Mage_Sales_Model_Order)
				||	($object instanceof Mage_Sales_Model_Order_Invoice)
				||  ($object instanceof Mage_Sales_Model_Order_Shipment))
			{
				if (!$object->getId())
				{
					Mage::register('smsnotify_object', $object, $graceful = true);
				}
			}
		}
		catch (Exception $e)
		{
			Mage::logException($e);
			Mage::log(__CLASS__.":".__METHOD__.":".$e->getMessage(), Zend_Log::ERR);
		}

		Mage::unregister('_helper/smsnotify/data');

		return $this;
	}


	/**
	 *
	 * @param Varien_Event_Observer $observer
	 * @return Artio_SMSNotifier_Model_Observer
	 */
	public function afterSaveAbstract($observer)
	{
		// get registered object (from beforeSaveAbstract)
		$registered = Mage::registry('smsnotify_object');

		// get saving object
		$event  = $observer->getEvent();
		$object = $event->getData('object');

		// if this does not match then we do not check this object type
		if ($object != $registered)
			return $this;

		// flush registered object
		Mage::unregister('smsnotify_object');

		try
		{
			// get helper
			$helper = Mage::helper('smsnotify');

			// get event code by object type
			$eventCode = '';

			if ($object instanceof Mage_Sales_Model_Order)
				$eventCode = Artio_SMSNotifier_Helper_Data::EVENT_NEW_ORDER;

			if ($object instanceof Mage_Sales_Model_Order_Invoice)
				$eventCode = Artio_SMSNotifier_Helper_Data::EVENT_NEW_INVOICE;

			if ($object instanceof Mage_Sales_Model_Order_Shipment)
				$eventCode = Artio_SMSNotifier_Helper_Data::EVENT_NEW_SHIPMENT;

			// unknow event?
			if (!$eventCode)
				return $this;

			// is there allowed send message?
			if (!$helper->isEventAllowed($eventCode, $object))
				return;

                        if (!$helper->isShippingMethodAllowed($eventCode, $object))
                                return;

			// get configuration
			$text	        = $helper->getText($eventCode, $object);
			$customerNumber = $helper->getCustomerNumber($eventCode, $object);
			$adminNumber    = $helper->getAdminNumber($eventCode, $object);
			$country        = $helper->getCountryCode($eventCode, $object);

			// process text message
			if ($eventCode == Artio_SMSNotifier_Helper_Data::EVENT_NEW_ORDER)
				$text = Mage::getModel('smsnotify/sms_template')->setOrder($object)->process($text);
			elseif ($eventCode == Artio_SMSNotifier_Helper_Data::EVENT_NEW_INVOICE)
				$text = Mage::getModel('smsnotify/sms_template')->setInvoice($object)->process($text);
			elseif ($eventCode == Artio_SMSNotifier_Helper_Data::EVENT_NEW_SHIPMENT)
				$text = Mage::getModel('smsnotify/sms_template')->setShipment($object)->process($text);

			// prepare sms
			$sms = Mage::getModel('smsnotify/sms');
			$sms->setStoreId($object->getStoreId());
			$sms->setText($text);
			$sms->addCustomData($eventCode, $object);

			// send to customer
			if ($customerNumber)
			{
				$sms->setType(Artio_SMSNotifier_Model_Sms::TYPE_CUSTOMER);
				$sms->setCountry($country);
				$sms->setNumber($customerNumber);
				$this->_getService()->send($sms);
			}

			// send to admin
			if ($adminNumber)
			{
				$sms->setType(Artio_SMSNotifier_Model_Sms::TYPE_ADMIN);
				$sms->setCountry('');
				$sms->setNumber($adminNumber);
				$this->_getService()->send($sms);
			}
		}
		catch (Exception $e)
		{
			Mage::logException($e);
			Mage::log(__CLASS__.":".__METHOD__.":".$e->getMessage(), Zend_Log::ERR);
		}

		Mage::unregister('_helper/smsnotify/data');

		return $this;
	}


	/**
	 * Get standard configuration model.
	 *
	 * @return Artio_SMSNotifier_Helper_Model_Config
	 */
	protected function _getConfig()
	{
		return Mage::getSingleton('smsnotify/config');
	}


	/**
	 * Get standard service.
	 *
	 * @return Artio_SMSNotifier_Model_Service
	 */
	protected function _getService()
	{
		return Mage::getSingleton('smsnotify/service');
	}

}