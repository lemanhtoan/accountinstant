<?php

class Magehit_Storecredit_Model_Mysql4_History extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('storecredit/history', 'history_id');
    }
}