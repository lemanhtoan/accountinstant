<?php
class Magehit_Storecredit_Block_Adminhtml_Customer_Edit_Tab_Form extends Mage_Adminhtml_Block_Template
{
	protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mh_storecredit/customer/edit/form.phtml');
    }
    
	protected function _prepareLayout()
    {
        $mh_form = $this->getLayout()
            ->createBlock('storecredit/adminhtml_customer_edit_tab_storecredit_form');

        $this->setChild('mh_storecredit_form', $mh_form);

        return parent::_prepareLayout();
    }
}
	