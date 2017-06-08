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
 * Multipayments Edit Block
 * 
 * @category     Magestore
 * @package     Magestore_Multipayments
 * @author      Magestore Developer
 */
class Magestore_Multipayments_Block_Adminhtml_Multipayments_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'multipayments';
        $this->_controller = 'adminhtml_multipayments';
        
        $this->_updateButton('save', 'label', Mage::helper('multipayments')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('multipayments')->__('Delete Item'));
        
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('multipayments_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'multipayments_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'multipayments_content');
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
        ";
    }
    
    /**
     * get text to show in header when edit an item
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('multipayments_data')
            && Mage::registry('multipayments_data')->getId()
        ) {
            return Mage::helper('multipayments')->__("Edit Item '%s'",
                                                $this->htmlEscape(Mage::registry('multipayments_data')->getTitle())
            );
        }
        return Mage::helper('multipayments')->__('Add Item');
    }
}