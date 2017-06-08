<?php

class Magestore_Imageoption_Model_Mysql4_Imageoption extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('imageoption/imageoption', 'imageoption_id');
    }
	
	public function loadIdByTypeOptionId($option_type_id)
	{
		$prefix = $this->getPrefixTable();
		
		$readAdapter = $this->_getReadAdapter();
		
		$select = $readAdapter->select()
					->from(array('imo'=>$prefix .'imageoption'),'imageoption_id')
					->where('option_type_id=?',$option_type_id);
		
		$imageoption_id = $readAdapter->fetchOne($select);
		
		return $imageoption_id ;
	}
	
	public function getOptionTitle($option_id)
	{
		$prefix = $this->getPrefixTable();
		$store_id = Mage::app()->getStore()->getId();
		$store_id = $store_id ? $store_id : 0;
		$readAdapter = $this->_getReadAdapter();
		
		$select = $readAdapter->select()
					->from(array('cpot'=>$prefix .'catalog_product_option_title'),'title')
					->where('option_id=?',$option_id)
					->where('store_id=?',$store_id);
		
		$title = $readAdapter->fetchOne($select);
		
		return $title ;		
	}

	function getOptionType($option_id)
	{
		$prefix = $this->getPrefixTable();
		$store_id = Mage::app()->getStore()->getId();
		$store_id = $store_id ? $store_id : 0;
		
		$readAdapter = $this->_getReadAdapter();
		
		$select = "SELECT cpott.title, cpott.option_type_id, cpo.product_id
				FROM ". $prefix ."catalog_product_option_type_title cpott, ". $prefix ."catalog_product_option_type_value cpotv, ". $prefix ."catalog_product_option cpo
				WHERE cpott.option_type_id = cpotv.option_type_id  
				AND cpotv.option_id = cpo.option_id 
				AND cpotv.option_id='$option_id' 
				AND cpott.store_id='$store_id' 
				ORDER BY cpotv.sort_order ASC, cpott.title ASC";
		
		$result = $readAdapter->fetchAll($select);
		
		if(!count($result)) {
			$store_id = 0;
			$select = "SELECT cpott.title, cpott.option_type_id, cpo.product_id
					FROM ". $prefix ."catalog_product_option_type_title cpott, ". $prefix ."catalog_product_option_type_value cpotv, ". $prefix ."catalog_product_option cpo
					WHERE cpott.option_type_id = cpotv.option_type_id  
					AND cpotv.option_id = cpo.option_id 
					AND cpotv.option_id='$option_id' 
					AND cpott.store_id='$store_id' 
					ORDER BY cpotv.sort_order ASC, cpott.title ASC";
			
			$result = $readAdapter->fetchAll($select);
		}
			
		return $result;				
	}	
	
	public function getOptionTypeTitle($option_type_id)
	{
		$prefix = $this->getPrefixTable();
		
		$readAdapter = $this->_getReadAdapter();
		
		$select = $readAdapter->select()
					->from(array('cpot'=>$prefix .'catalog_product_option_type_title'),'title')
					->where('option_type_id=?',$option_type_id);
		
		$title = $readAdapter->fetchOne($select);
		
		return $title ;		
	}
	
	public function getOptionTypeIdByTitle($product,$option_title,$option_type_title)
	{
		$option_id = 0;
		$option_type_id = 0;
		
		$prefix = $this->getPrefixTable();
		
		$readAdapter = $this->_getReadAdapter();
		
		$select_option_id = "SELECT cpot.option_id
							FROM ". $prefix ."catalog_product_option_title cpot, "
							. $prefix ."catalog_product_option cpo 
							WHERE cpot.option_id = cpo.option_id 
							AND cpot.title = '". $option_title ."' 
							AND cpo.product_id = '". $product->getId() ."'";
		
		$option_id = $readAdapter->fetchOne($select_option_id);
		
		$select_option_type_id = "SELECT  cpott.option_type_id 
							FROM ". $prefix ."catalog_product_option_type_title cpott, ". $prefix ."catalog_product_option_type_value cpotv
							WHERE cpott.option_type_id = cpotv.option_type_id 
							AND cpotv.option_id='$option_id' 
							AND cpott.title='$option_type_title' 
							AND cpott.store_id='0'";	
		
		$option_type_id = $readAdapter->fetchOne($select_option_type_id);
		
		return array('option_id' => $option_id, 'option_type_id' => $option_type_id) ;				
	}
	
	public function getPrefixTable()
	{
		return str_replace('imageoption','',$this->getTable('imageoption'));
	}
}