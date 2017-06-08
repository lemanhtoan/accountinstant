<?php

class Magehit_Storecredit_Block_Adminhtml_Renderer_Transaction extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	
    	if (empty($row['history_id'])) return '';
    	$result_mini = '';
    	$history = Mage::getModel('storecredit/history')->load($row['history_id']);
    	$type = $history->getTransactionType();
    	$transaction_params = $history->getTransactionParams();
    	$order_id = $history->getOrderId();

    	$result = Magehit_Storecredit_Model_Type::getTransactionDetail($type,$transaction_params,$order_id,true); 
    	
    	return $result;
    }

}