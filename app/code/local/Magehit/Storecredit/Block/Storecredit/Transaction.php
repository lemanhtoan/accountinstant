<?php
class Magehit_Storecredit_Block_Storecredit_Transaction extends Mage_Core_Block_Template
{
	
	public function __construct()
    {
        parent::__construct();

        $customer_id = $this->_getCustomer()->getId();
        $credits = Mage::getModel('storecredit/history')->getCollection()
					->addFilter('customer_id',$customer_id)
					->setOrder('history_id', 'DESC');

        $this->setCreditHistory($credits);	// set data for display to frontend
    }
    
	private function _getCustomer()
	{
		return Mage::getSingleton("customer/session")->getCustomer();
	}
	
	public function getTransactionDetail($type, $transaction_params,$order_id)
	{
		return Magehit_Storecredit_Model_Type::getTransactionDetail($type,$transaction_params,$order_id);
	}
	
	public function getStatusText($status)
	{
		return Magehit_Storecredit_Model_Statushistory::getLabel($status);
	}
	public function getAmount($amount, $type)
	{
		return Magehit_Storecredit_Model_Type::getAmountWithSign($amount, $type);
	}

	// prepare navigation
	public function _prepareLayout()
    {
		parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'customer_storecredit_transaction')
					  ->setCollection($this->getCreditHistory());	// set data for navigation
        $this->setChild('pager', $pager);
        return $this;
    }
	
	public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}