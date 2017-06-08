<?php

class Magestore_Imageoption_Block_Adminhtml_Template_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('imageoptiontemplate_form', array('legend'=>Mage::helper('imageoption')->__('Item information')));
     
      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('imageoption')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
	  
      $fieldset->addField('short_descrip', 'text', array(
          'label'     => Mage::helper('imageoption')->__('Short Description'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'short_descrip',
      ));	  

		
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('imageoption')->__('Status'),
          'name'      => 'template_status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('imageoption')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('imageoption')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('description', 'textarea', array(
          'name'      => 'description',
          'label'     => Mage::helper('imageoption')->__('Description'),
          'title'     => Mage::helper('imageoption')->__('Description'),
          'style' => 'width: 98%; height: 100px;',
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getImageoptionData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getImageoptionData());
          Mage::getSingleton('adminhtml/session')->setImageoptionData(null);
      } elseif ( Mage::registry('template_data') ) {
          $form->setValues(Mage::registry('template_data')->getData());
      }
      return parent::_prepareForm();
  }
}