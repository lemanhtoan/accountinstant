<?php

class Thienha_Webmoney_Block_Webmoney_Info extends Mage_Payment_Block_Info
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('webmoney/info/info.phtml');
    }
	
	public function getInfo()
    {
        $info = $this->getData('info');
        
        if (!($info instanceof Mage_Payment_Model_Info)) {
            Mage::throwException($this->__('Can not retrieve payment info model object.'));
        }
        return $info;
    }
    
    public function getMethod()
    {
        return $this->getInfo()->getMethodInstance();
    }
	
    public function toPdf()
    {
        $this->setTemplate('webmoney/info/pdf/info.phtml');
        return $this->toHtml();
    }	

	public function getOrderAdmin()
	{
		$order = Mage::registry('current_order');
		if ( is_null($order) )
		{
			$_session = Mage::getSingleton('adminhtml/session_quote');
			$order = $_session->getOrder();
		}
		if (!$order->getId())
		{
			$orderId = $this->getRequest()->getParam('order_id');
			$order = Mage::getModel('sales/order')->load($orderId);
			
			if ( !$order->getId() )
			{
				$invoice = Mage::registry('current_invoice');
				if ( !is_null($invoice) )
					$order = $invoice->getOrder();
			}
		}
		return $order;
	}

	public function getOrder()
	{
		$orderId = $this->getRequest()->getParam('order_id');
		$order = Mage::getModel('sales/order')->load($orderId);			
		if (!$order->getId()) {
			$order = Mage::registry('current_order');			
		}
		return $order;
	}
	
	public function getTransactionData($order)
	{
		$resource = Mage::getSingleton('core/resource');
		$read = $resource->getConnection('core_read');
		$wmTable = $resource->getTableName('thienha_webmoney');
		$select = $read->select();
		$select->from($wmTable,array('transaction_id','state','timestamp','rnd','lmi_sys_invs_no','lmi_sys_trans_no','lmi_sys_trans_date','lmi_payer_purse','lmi_payer_wm'))
			->where('order_id=?',$order->getId());
		$dbData = $read->fetchRow($select);
		return $dbData;
	}
	
}