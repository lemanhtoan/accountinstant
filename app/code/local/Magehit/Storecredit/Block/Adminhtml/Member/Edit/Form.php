<?php

class Magehit_Storecredit_Block_Adminhtml_Member_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form_member_detail = new Varien_Data_Form(array(
                                      'id' => 'edit_form',
                                      'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                                      'method' => 'post',
        							  'enctype' => 'multipart/form-data'
                                   )
      );

      $form_member_detail->setUseContainer(true);
      $this->setForm($form_member_detail);
      return parent::_prepareForm();
  }
}