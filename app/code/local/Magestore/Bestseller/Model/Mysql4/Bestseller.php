<?php

class Magestore_Bestseller_Model_Mysql4_Bestseller extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the bestseller_id refers to the key field in your database table.
        $this->_init('bestseller/bestseller', 'bestseller_id');
    }
	public function getCatalogBrand($allStore = false)
	{			
		$select = $this->_getReadAdapter()->select()
					->from(array('eao'=> 'eav_attribute_option'),array('option_id','eaov.value','eaov.store_id'))
					->join(array('ea'=> 'eav_attribute'),'eao.attribute_id=ea.attribute_id',array())
					->join(array('eaov'=> 'eav_attribute_option_value'),'eao.option_id=eaov.option_id',array())
					->where('ea.attribute_code=?','days');
        if($allStore)
            $select->where('eaov.store_id=?',0);
        else {
            $select->where('eaov.store_id !=?',0);
        }
		$option = $this->_getReadAdapter()->fetchAll($select);
		$arr=array();
		foreach($option as $value){
			$arr[$value['option_id']]=$value['value'];
		}
		return $arr;	
	}
}