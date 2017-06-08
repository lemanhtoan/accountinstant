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
 * Gateway.
 *
 * @category Artio
 * @package Artio_SMSNotifier
 * @author Artio Magento Team <info@artio.net>
 */
class Artio_SMSNotifier_Model_System_Config_Source_SendToAdmin
{


    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
    	return $this->toOptions();
    }


    /**
     * Options getter
     *
     * @return array
     */
    public function toOptions()
    {
    	$config = Mage::getSingleton('smsnotify/config');

    	$options = array();

    	// No option
    	$options[] = array(
    		'value' => '0',
    		'label' => Mage::helper('smsnotify')->__('No')
    	);

    	// Primary admin option
    	$primary = $config->getPureAdminNumberByIndex(1);

    	if ($primary)
    	{
    		$options[] = array(
    			'value' => '1',
    			'label' => Mage::helper('smsnotify')->__('Primary number (%s)', $config->sanitizeNumber($primary))
    		);
    	}
    	else
    	{
    		$options[] = array(
    			'value'    => '1',
    			'label'    => Mage::helper('smsnotify')->__('Primary number (not set)'),
    			'disabled' => 'disabled'
    		);
    	}


    	// Secondary admin option
    	$secondary = $config->getPureAdminNumberByIndex(2);

    	if ($secondary)
    	{
    		$options[] = array(
    			'value' => '2',
    			'label' => Mage::helper('smsnotify')->__('Secondary number (%s)', $config->sanitizeNumber($secondary))
    		);
    	}
    	else
    	{
    		$options[] = array(
    			'value'    => '2',
    			'label'    => Mage::helper('smsnotify')->__('Secondary number (not set)'),
    			'disabled' => 'disabled'
    		);
    	}

    	return $options;
    }


}