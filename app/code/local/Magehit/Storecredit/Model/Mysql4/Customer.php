<?php

class Magehit_Storecredit_Model_Mysql4_Customer extends Mage_Core_Model_Mysql4_Abstract
{
	protected $_isPkAutoIncrement    = false;
    public function _construct()
    {    
        $this->_init('storecredit/customer', 'customer_id');
    }
}