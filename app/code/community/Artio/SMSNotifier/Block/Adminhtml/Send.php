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
 * Send form.
 *
 * @category    Artio
 * @package     Artio_SMSNotifier
 * @author      Artio Magento Team (info@artio.net)
 */
class Artio_SMSNotifier_Block_Adminhtml_Send extends Mage_Adminhtml_Block_Widget_Form_Container
{

    public function __construct()
    {
        $this->_objectId   = 'page_id';
        $this->_blockGroup = 'smsnotify';
        $this->_controller = 'adminhtml';

        parent::__construct();

        $this->_removeButton('reset');
        $this->_removeButton('save');

        $this->_addButton('save', array(
        		'label'     => Mage::helper('smsnotify')->__('Send SMS'),
        		'onclick'   => 'editForm.submit();',
        		'class'     => 'save',
        ), 1);
    }

    /**
     * Retrieve text for header element depending on loaded page
     *
     * @return string
     */
    public function getHeaderText() {
    	return Mage::helper('smsnotify')->__('SMS Notifier - Send SMS');
    }

}
