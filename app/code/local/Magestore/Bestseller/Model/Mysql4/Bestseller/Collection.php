<?php

class Magestore_Bestseller_Model_Mysql4_Bestseller_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('bestseller/bestseller');
    }
}