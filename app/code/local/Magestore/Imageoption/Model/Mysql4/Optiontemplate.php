<?php

class Magestore_Imageoption_Model_Mysql4_Optiontemplate extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('imageoption/optiontemplate', 'template_option_id');
    }
}