<?php

class Magehit_Storecredit_Block_Adminhtml_Member_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('member_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('storecredit')->__('Credit Member'));
  }

  protected function _beforeToHtml()
  {   
  	  
      $this->addTab('form_member_detail', array(
          'label'     => Mage::helper('storecredit')->__('Credit Member Information'),
          'title'     => Mage::helper('storecredit')->__('Credit Member Information'),
          'content'   => $this->getLayout()->createBlock('storecredit/adminhtml_member_edit_tab_form')->toHtml(),
      ));

      $this->addTab('form_member_transaction', array(
          'label'     => Mage::helper('storecredit')->__('Transaction Credit History'),
          'title'     => Mage::helper('storecredit')->__('Transaction Credit History'),
          'content'   => $this->getLayout()->createBlock('storecredit/adminhtml_member_edit_tab_transaction')->toHtml(),
          ));
     
      return parent::_beforeToHtml();
  }
}