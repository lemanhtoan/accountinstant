<?php
class Websmart_ManageKeys_Model_Mysql4_Managekeys extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('managekeys/managekeys', 'id');
    }
}