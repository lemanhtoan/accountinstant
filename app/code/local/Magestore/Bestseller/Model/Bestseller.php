<?php

class Magestore_Bestseller_Model_Bestseller extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('bestseller/bestseller');
    }
}