<?php

class Magestore_Imageoption_Model_Optiontypesmap extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imageoption/optiontypesmap');
    }
	
	public function getPrdOpTypeIdByTmplOpTypeId($tmp_op_type_id)
	{
		$collection = $this->getCollection()
						->addFieldToFilter('template_option_type_id',$tmp_op_type_id);
		
		if(count($collection))
		{
			foreach($collection as $item){}
		
			return $item->getProductOptionTypeId();
		}
	
	}	
	
}