<?php

class Magestore_Imageoption_Model_Optiontemplate extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imageoption/optiontemplate');
    }
	
	public function loadByTempIdOpId($template_id,$option_id)
	{
		$collection = $this->getCollection()
						->addFieldToFilter('template_id',$template_id)
						->addFieldToFilter('option_id',$option_id);
		
		if(! count($collection))
			return $this;
			
		foreach($collection as $item){}
		
		return $item;
	}
}