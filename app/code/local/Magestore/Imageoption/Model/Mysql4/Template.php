<?php

class Magestore_Imageoption_Model_Mysql4_Template extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('imageoption/template', 'template_id');
    }
}