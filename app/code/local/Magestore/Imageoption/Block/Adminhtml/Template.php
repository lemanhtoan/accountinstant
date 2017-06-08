<?php
class Magestore_Imageoption_Block_Adminhtml_Template extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_template';
    $this->_blockGroup = 'imageoption';
    $this->_headerText = Mage::helper('imageoption')->__('Imageoption Templates Manager');
    $this->_addButtonLabel = Mage::helper('imageoption')->__('Add Imageoption Template');
    parent::__construct();
  }
}