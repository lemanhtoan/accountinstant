<?php

class Magestore_Imageoption_Block_Adminhtml_Template_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('imageoptiontemplate_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('imageoption')->__('Imageoption Template Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('imageoption')->__('Imageoption Template Information'),
          'title'     => Mage::helper('imageoption')->__('Imageoption Template Information'),
          'content'   => $this->getLayout()->createBlock('imageoption/adminhtml_template_edit_tab_form')->toHtml(),
    
	));
     
     $this->addTab('imageoptions_section', array(
            'label' => Mage::helper('catalog')->__('Options'),
            'url'   => $this->getUrl('*/*/options', array('_current' => true, 'id' => $this->getRequest()->getParam('id'), 'store'=> $this->getRequest()->getParam('store'))),
            'class' => 'ajax',
     ));	 
	 
     $this->addTab('related', array(
        'label'     => Mage::helper('catalog')->__('Assigned Products'),
        'url'   => $this->getUrl('*/*/listproduct', array('_current' => true, 'id' => $this->getRequest()->getParam('id'), 'store'=> $this->getRequest()->getParam('store'))),
        'class' => 'ajax',
     ));	 
	 
     return parent::_beforeToHtml();
  }
}