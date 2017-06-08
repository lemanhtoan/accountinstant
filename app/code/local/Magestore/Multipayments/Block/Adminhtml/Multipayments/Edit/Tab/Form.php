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
 * Multipayments Edit Form Content Tab Block
 * 
 * @category    Magestore
 * @package     Magestore_Multipayments
 * @author      Magestore Developer
 */
class Magestore_Multipayments_Block_Adminhtml_Multipayments_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Magestore_Multipayments_Block_Adminhtml_Multipayments_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getMultipaymentsData()) {
            $data = Mage::getSingleton('adminhtml/session')->getMultipaymentsData();
            Mage::getSingleton('adminhtml/session')->setMultipaymentsData(null);
        } elseif (Mage::registry('multipayments_data')) {
            $data = Mage::registry('multipayments_data')->getData();
        }
        $fieldset = $form->addFieldset('multipayments_form', array(
            'legend'=>Mage::helper('multipayments')->__('Item information')
        ));

        $fieldset->addField('title', 'text', array(
            'label'        => Mage::helper('multipayments')->__('Title'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'title',
        ));

        $fieldset->addField('filename', 'file', array(
            'label'        => Mage::helper('multipayments')->__('File'),
            'required'    => false,
            'name'        => 'filename',
        ));

        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('multipayments')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('multipayments/status')->getOptionHash(),
        ));

        $fieldset->addField('content', 'editor', array(
            'name'        => 'content',
            'label'        => Mage::helper('multipayments')->__('Content'),
            'title'        => Mage::helper('multipayments')->__('Content'),
            'style'        => 'width:700px; height:500px;',
            'wysiwyg'    => false,
            'required'    => true,
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}