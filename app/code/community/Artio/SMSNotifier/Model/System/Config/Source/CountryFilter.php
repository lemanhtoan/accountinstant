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
 * Country filter.
 *
 * @category Artio
 * @package Artio_SMSNotifier
 * @author Artio Magento Team <info@artio.net>
 */
class Artio_SMSNotifier_Model_System_Config_Source_CountryFilter
{


    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	return array(
    		array('value' => 'local', 		'label' => Mage::helper('smsnotify')->__('Only local country')),
    		array('value' => 'everywhere',	'label' => Mage::helper('smsnotify')->__('Everywhere')),
    		array('value' => 'specific',	'label' => Mage::helper('smsnotify')->__('Only specific countries'))
    	);
    }


}