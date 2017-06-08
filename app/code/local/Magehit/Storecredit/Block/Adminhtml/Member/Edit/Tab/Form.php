<?php

class Magehit_Storecredit_Block_Adminhtml_Member_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {   
      $form_member_detail = new Varien_Data_Form();
      $this->setForm($form_member_detail);
      
      $fieldset1 = $form_member_detail->addFieldset('base_fieldset', array('legend'=>Mage::helper('storecredit')->__('Credit Member Information')));
      
      $fieldset = $form_member_detail->addFieldset('credit_form', array('legend'=>Mage::helper('storecredit')->__('Manually Update Credits')));
      
      $customer_id = $this->getRequest()->getParam('id');      
      $customer = Mage::getModel('customer/customer')->load($customer_id);
	  $customer_name = $customer->getName();
	  $credit = Mage::getModel('storecredit/customer')->load($customer_id)->getData('credit_balance');
	  
       $fieldset1->addField('customer_email', 'note', array(
          'label'     => Mage::helper('storecredit')->__('Customer Name'),
          'text'      => Mage::helper('storecredit')->getLinkCustomer($customer_id,$customer_name),
      ));
      
      $fieldset1->addField('storecredit', 'note', array(
          'label'     => Mage::helper('storecredit')->__('Credits'),
      	  'name'  	=> 'mh_storecredit', 
          'text'     => Mage::helper('core')->currency($credit),
      ));

      $fieldset->addField('amount', 'text',
             array(
                    'label' 	=> Mage::helper('storecredit')->__('Amount'),
                    'name'  	=> 'mh_storecredit_amount',
             		'class'		=> 'validate-digits'
             )
        );
        
        $fieldset->addField('action', 'select',
             array(
                    'label' 	=> Mage::helper('storecredit')->__('Action'),
                    'name'  	=> 'mh_storecredit_action',
             		'options'	=> Mage::getModel('storecredit/action')->getOptionArray()
             )
        );
        
        $fieldset->addField('comment', 'textarea',
             array(
                    'label' 	=> Mage::helper('storecredit')->__('Message'),
                    'name'  	=> 'mh_storecredit_comment',
             		'style'		=>	'height:100px'
             )
        );
      $form_member_detail->getElement('action')->setValue(1);
      
      return parent::_prepareForm();
  }

}