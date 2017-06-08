<?php

class Magehit_Storecredit_Block_Adminhtml_Renderer_Amount extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	if (empty($row['history_id'])) return '';
    	$history = Mage::getModel('storecredit/history')->load($row['history_id']);
		$result = '';
		$amount = Mage::helper('core')->currency($history->getAmount(),false,false);
    	$result = Mage::getModel('storecredit/type')->getAmountWithSign($amount,$history->getTransactionType()); 
    	return $result;
    }

}