<?php

class Magehit_Storecredit_Model_Mysql4_Customer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('storecredit/customer');
    }
}