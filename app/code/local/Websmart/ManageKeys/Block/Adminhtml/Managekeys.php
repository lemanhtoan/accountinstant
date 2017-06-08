<?php
class Websmart_ManageKeys_Block_Adminhtml_Managekeys extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_managekeys';
        $this->_blockGroup = 'managekeys';
        $this->_headerText = Mage::helper('managekeys')->__('Key Manager');
        $this->_addButtonLabel = Mage::helper('managekeys')->__('Add Key');
        parent::__construct();
    }
}