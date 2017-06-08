<?php
class Websmart_ManageKeys_Block_Adminhtml_Managekeys_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        
        $this->_objectId = 'id';
        $this->_blockGroup = 'managekeys';
        $this->_controller = 'adminhtml_managekeys';
        
        $this->_updateButton('save', 'label', Mage::helper('managekeys')->__('Save Key'));
        $this->_updateButton('delete', 'label', Mage::helper('managekeys')->__('Delete Key'));
        
        $this->_addButton('saveandcontinue', array(
            'label'        => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'    => 'saveAndContinueEdit()',
            'class'        => 'save',
        ), -100);

        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('managekeys_content') == null)
                    tinyMCE.execCommand('mceAddControl', false, 'managekeys_content');
                else
                    tinyMCE.execCommand('mceRemoveControl', false, 'managekeys_content');
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
        if (Mage::registry('managekeys_data')
            && Mage::registry('managekeys_data')->getId()
        ) {
            return Mage::helper('managekeys')->__("Edit Key '%s'",
                                                $this->htmlEscape(Mage::registry('managekeys_data')->getKey())
            );
        }
        return Mage::helper('managekeys')->__('Add Key');
    }
}