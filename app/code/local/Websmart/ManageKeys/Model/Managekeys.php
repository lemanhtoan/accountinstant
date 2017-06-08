<?php
class Websmart_ManageKeys_Model_Managekeys extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('managekeys/managekeys');
    }
}