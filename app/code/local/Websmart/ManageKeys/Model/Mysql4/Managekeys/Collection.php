<?php
class Websmart_ManageKeys_Model_Mysql4_Managekeys_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('managekeys/managekeys');
    }
	public function getAllKeys(){
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Zend_Db_Select::ORDER);
        $idsSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $idsSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $idsSelect->reset(Zend_Db_Select::COLUMNS);
        $idsSelect->columns('main_table.key');
        $idsSelect->resetJoinLeft();
        return $this->getConnection()->fetchCol($idsSelect);
    }
}