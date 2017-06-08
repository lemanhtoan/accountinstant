<?php

class Magestore_Imageoption_Model_Mysql4_Producttemplate extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('imageoption/producttemplate', 'template_product_id');
    }
}