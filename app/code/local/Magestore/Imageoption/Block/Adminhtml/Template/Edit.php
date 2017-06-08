<?php

class Magestore_Imageoption_Block_Adminhtml_Template_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'imageoption';
        $this->_controller = 'adminhtml_template';
        
        $this->_addButton('apply', array(
            'label'     => Mage::helper('adminhtml')->__('Apply Template'),
            'onclick'   => 'applyTemplate()',
            'class'     => 'save',
        ), 0);		
		
        $this->_updateButton('save', 'label', Mage::helper('imageoption')->__('Save'));
        $this->_updateButton('delete', 'label', Mage::helper('imageoption')->__('Delete'));
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);
		
		
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('imageoption_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'imageoption_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'imageoption_content');
                }
            }

            function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			
			function applyTemplate(){
				if(confirm('Do you want to apply?'))
				{
					editForm.submit($('edit_form').action+'apply/edit/');
				}
			}
			
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('template_data') && Mage::registry('template_data')->getId() ) {
            return Mage::helper('imageoption')->__("Edit Imageoption Template ", $this->htmlEscape(Mage::registry('template_data')->getTitle()));
        } else {
            return Mage::helper('imageoption')->__('Add Imageoption Template');
        }
    }
}