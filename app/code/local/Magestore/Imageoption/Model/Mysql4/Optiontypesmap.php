<?php

class Magestore_Imageoption_Model_Mysql4_Optiontypesmap extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('imageoption/optiontypesmap', 'optiontypemap_id');
    }
	
	public function getPrdOpTypeIds($tmplOpId,$tmplOpTypeIds)
	{
		$tmplOpTypeIds = implode(',',$tmplOpTypeIds);
		
		$sql = $this->_getReadAdapter()->select()
				->from(array('itpotm'=> $this->getTable('imageoption/optiontypesmap')),'product_option_type_id')
				->join(array('itpom' => $this->getTable('imageoption/optionsmap')),'itpotm.optionmap_id = itpom.optionmap_id',array())
				->where('itpom.template_option_id = ?',$tmplOpId)
				->where('itpotm.template_option_type_id NOT IN (?)',$tmplOpTypeIds)
				;
		$options = $this->_getReadAdapter()->fetchAll($sql);
		
		return $options;
	}
	
	public function getTmplOpTypeIds($tmplOpId)
	{	
		$sql = $this->_getReadAdapter()->select()
				->from(array('itpotm'=> $this->getTable('imageoption/optiontypesmap')),'template_option_type_id')
				->join(array('itpom' => $this->getTable('imageoption/optionsmap')),'itpotm.optionmap_id = itpom.optionmap_id',array())
				->where('itpom.template_option_id = ?',$tmplOpId)
				;
		$options = $this->_getReadAdapter()->fetchAll($sql);
		
		return $options;
	}	
	
}