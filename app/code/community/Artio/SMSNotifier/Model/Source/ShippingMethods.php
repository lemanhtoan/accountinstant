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
class Artio_SMSNotifier_Model_Source_ShippingMethods
{

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options =  array();

        foreach (Mage::app()->getStore()->getConfig('carriers') as $code => $carrier)
        {
            if (isset($carrier['title']))
            {
                $options[] = array(
                    'value' => $code,
                    'label' => $carrier['title']
                );
            }
        }
        return $options;
    }

}