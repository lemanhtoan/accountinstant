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
 * Standard helper.
 *
 * @category Artio
 * @package Artio_SMSNotifier
 * @author Artio Magento Team <info@artio.net>
 */
class Artio_SMSNotifier_Helper_SMSLog extends Mage_Core_Helper_Abstract
{


	/**
	 * Log sended SMS to file var/log/smsnotify.log.
	 * Log must be enabled other method does nothing.
	 *
	 * Method return $this for keep the influence interface.
	 *
	 * @param Artio_SMSNotifier_Model_Sms $sms
	 * @return Artio_SMSNotifier_Helper_SMSLog $this
	 */
	public function logSendedSMS(Artio_SMSNotifier_Model_Sms $sms)
	{
		$text = sprintf("SENT (%s)", $this->_smsToString($sms));

		Mage::log($text, Zend_Log::DEBUG, 'smsnotify.log');

		return $this;
	}


	/**
	 * Log sended SMS to file var/log/smsnotify.log.
	 * Log must be enabled other method does nothing.
	 *
	 * Method return $this for keep the influence interface.
	 *
	 * @param Artio_SMSNotifier_Model_Sms $sms
	 * @param string $errorMessage
	 * @return Artio_SMSNotifier_Helper_SMSLog
	 */
	public function logNotSendedSMS(Artio_SMSNotifier_Model_Sms $sms, $errorMessage = '')
	{
		$text = sprintf("NOT SENT (error: %s) (%s)", $errorMessage, $this->_smsToString($sms));

		Mage::log($text, Zend_Log::DEBUG, 'smsnotify.log');

		return $this;
	}


	/**
	 * Convert $sms to string.
	 *
	 * @param Artio_SMSNotifier_Model_Sms $sms
	 * @return string
	 */
	protected function _smsToString(Artio_SMSNotifier_Model_Sms $sms)
	{
		return sprintf("Type: %s; Number: %s; Text: %s",
					$sms->isCustomerSMS() ? 'customer' : 'administrator',
					$sms->getNumber(),
					$sms->getText());
	}

}