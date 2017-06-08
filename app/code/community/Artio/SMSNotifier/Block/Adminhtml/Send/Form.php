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
class Artio_SMSNotifier_Block_Adminhtml_Send_Form extends Mage_Adminhtml_Block_Widget_Form
{


	/**
	 * Prepare form.
	 */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
        	'name'   => 'send_form',
        	'id'     => 'edit_form',
        	'action' => $this->getData('action'), 'method' => 'post')
        );

        $fieldset = $form->addFieldset('sendsms_fieldset', array(
        	'legend' => Mage::helper('smsnotify')->__('Telephone Number List'),
        	'class'  => 'fieldset'
        ));

        $fieldset->addType('customer_numbers', 'Artio_SMSNotifier_Block_Adminhtml_Data_Form_Element_CustomerNumbers');
        $fieldset->addField('customer_numbers', 'customer_numbers', array(
        	'name'  => 'customer_numbers',
        	'label' => Mage::helper('smsnotify')->__('Telephone Number List')
        ));

        $fieldset->addField('admin', 'selectex', array(
        	'name'	  => 'admin',
        	'label'	  => Mage::helper('smsnotify')->__('Send to administrator'),
        	'options' => Mage::getModel('smsnotify/system_config_source_sendToAdmin')->toOptions()
        ));

        $fieldset2 = $form->addFieldset('textsms_fieldset', array(
        	'legend' => Mage::helper('smsnotify')->__('SMS Text'),
        	'class'  => 'fieldset-wide'
        ));

        $fieldset2->addType('smstextarea', 'Varien_Data_Form_Element_Smstextarea');
        $fieldset2->addField('sms_text', 'smstextarea', array(
        	'name'		=> 'sms_text',
        	'label'		=> Mage::helper('smsnotify')->__('Message'),
        	'required'	=> true
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }


}
