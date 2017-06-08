<?php
class Magestore_Imageoption_Block_Adminhtml_Imageoption extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_imageoption';
    $this->_blockGroup = 'imageoption';
    $this->_headerText = Mage::helper('imageoption')->__('Item Manager');
    $this->_addButtonLabel = Mage::helper('imageoption')->__('Add Item');
    parent::__construct();
  }
}