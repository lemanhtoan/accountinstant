<?php

class Magestore_Imageoption_Model_Producttemplate extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imageoption/producttemplate');
    }
	
	public function loadByTempIdPrdId($template_id,$product_id)
	{
		$collection = $this->getCollection()
						->addFieldToFilter('template_id',$template_id)
						->addFieldToFilter('product_id',$product_id);
		
		if(! count($collection))
			return $this;
			
		foreach($collection as $item){}
		
		return $item;		
	}
	
	public function apply()
	{
		if(! $this->getId())
			return;
		
		$templateOptions = Mage::getModel('imageoption/optiontemplate')->getCollection()
							->addFieldToFilter('template_id',$this->getTemplateId());
		
		
		$optionMap = Mage::getModel('imageoption/optionsmap');
		$optiontypeMap = Mage::getModel('imageoption/optiontypesmap');
		$optionInstance = Mage::getModel('imageoption/productoption');
		$imageoption = Mage::getModel('imageoption/imageoption');
		$product = Mage::getModel('catalog/product')->load($this->getProductId());
		$is_required_option = $product->getData('required_options');

		foreach($templateOptions as $templateOption)
		{
				//duplicate option
			$optionInstance->load($templateOption->getOptionId());
			$mapInfo = $optionInstance->duplicate(0,$this->getProductId());
			$is_required_option = ($optionInstance->getIsRequire() == 1) ? 1 : $is_required_option;
	
				// mapping option
			$optionMap->setId(null);
			$optionMap->setTemplateProductId($this->getId());
			$optionMap->setTemplateOptionId($templateOption->getOptionId());
			$optionMap->setProductOptionId($mapInfo['option'][$templateOption->getOptionId()]);
			$optionMap->save();
				//mapping option_type
			if(count($mapInfo['option_type']))
			{
				foreach($mapInfo['option_type'] as $tmpl_option_type_id => $prd_option_type_id)
				{
					$optiontypeMap->setId(null);
					$optiontypeMap->setOptionmapId($optionMap->getId());
					$optiontypeMap->setTemplateOptionTypeId($tmpl_option_type_id);
					$optiontypeMap->setProductOptionTypeId($prd_option_type_id);
					$optiontypeMap->save();
				}
			}
			
				//generate option images
			$imageoption->generateOptionImage($optionInstance,$mapInfo['option'][$templateOption->getOptionId()],$this->getProductId());
						
		}		
		
		$product->setData('has_options',1)
				->setData('required_options',$is_required_option)
				->save();				
	}

	public function assignProduct()
	{
		if(! $this->getId())
			return;
		
		$maps = Mage::getModel('imageoption/optionsmap')->getCollection()
							->addFieldToFilter('template_product_id',$this->getId());
		
		$assginedOptionIds = array();
		if(count($maps))
		{
			foreach($maps as $map)
			{
				$assginedOptionIds[] = $map->getTemplateOptionId();
			}
		} else {
			$assginedOptionIds = array(0);
		}
		
		$templateOptions = Mage::getModel('imageoption/optiontemplate')->getCollection()
							->addFieldToFilter('template_id',$this->getTemplateId())
							->addFieldToFilter('option_id',array('nin'=>$assginedOptionIds));
		
			//hasn't new optinons
		if(! count($templateOptions))
		{
			$templateOptions =  Mage::getModel('imageoption/optiontemplate')->getCollection()
									->addFieldToFilter('template_id',$this->getTemplateId());
			$optionIds = array();
			
			if(! count($templateOptions))
			{
				$optionIds = array(0);
			}else{
				foreach($templateOptions as $templateOption )
				{
					$optionIds[] = $templateOption->getOptionId();
				}
			}
			$maps = Mage::getModel('imageoption/optionsmap')->getCollection()
							->addFieldToFilter('template_product_id',$this->getId())			
							->addFieldToFilter('template_option_id',array('nin'=>$optionIds));
			if(count($maps))
			{
				foreach($maps as $map)
				{
						//delete old options
					$optionInstance = Mage::getModel('imageoption/productoption')->load($map->getProductOptionId());
					$optionInstance->delete();
						//unmapping
					$map->delete();
				}
			}
				//synch option_type product vs template
			$this->synchOptionType($optionIds);
			
			return;
		}
			//has new optinons
		$optionMap = Mage::getModel('imageoption/optionsmap');
		$optiontypeMap = Mage::getModel('imageoption/optiontypesmap');
		$optionInstance = Mage::getModel('imageoption/productoption');
		$imageoption = Mage::getModel('imageoption/imageoption');
		$is_required_option = 0;
		foreach($templateOptions as $templateOption)
		{
				//duplicate option
			$optionInstance->load($templateOption->getOptionId());
			$mapInfo = $optionInstance->duplicate(0,$this->getProductId());
			$is_required_option = ($optionInstance->getIsRequire() == 1) ? 1 : $is_required_option;
				
				// mapping option
			$optionMap->setId(null);
			$optionMap->setTemplateProductId($this->getId());
			$optionMap->setTemplateOptionId($templateOption->getOptionId());
			$optionMap->setProductOptionId($mapInfo['option'][$templateOption->getOptionId()]);
			$optionMap->save();
				//mapping option_type
			if(count($mapInfo['option_type']))
			{
				foreach($mapInfo['option_type'] as $tmpl_option_type_id => $prd_option_type_id)
				{
					$optiontypeMap->setId(null);
					$optiontypeMap->setOptionmapId($optionMap->getId());
					$optiontypeMap->setTemplateOptionTypeId($tmpl_option_type_id);
					$optiontypeMap->setProductOptionTypeId($prd_option_type_id);
					$optiontypeMap->save();
				}
			}
			
				//generate option images
			$imageoption->generateOptionImage($optionInstance,$mapInfo['option'][$templateOption->getOptionId()],$this->getProductId());
						
		}		
		
		Mage::getModel('catalog/product')->load($this->getProductId())
											->setData('has_options',1)
											->setData('required_options',$is_required_option)
											->save();		
	}	
	
	public function unassignProduct()
	{
			//remove product options
		$optionMaps = Mage::getModel('imageoption/optionsmap')->getCollection()
							->addFieldToFilter('template_product_id',$this->getId());
		if(count($optionMaps))
		{
			foreach($optionMaps as $optionMap)
			{
					//delete product option
				$optionInstance = Mage::getModel('imageoption/productoption')->load($optionMap->getProductOptionId());
				$optionInstance->delete();
					//unmapping
				$optionMap->delete();
			}
		}
	}
	
	public function synchOptionType($optionIds)
	{
			//get All product option type
		if(! count($optionIds))
			return;
		
		$readAdapter = Mage::getModel('core/resource')->getConnection('core_read');
		
		$optionvalue = Mage::getModel('imageoption/optionvalue');
		
		foreach($optionIds as $optionId)
		{
				//get all current template_option_type
			$producOptionTypes = $optionvalue->getCollection()
									->addFieldToFilter('option_id',$optionId);
			
			$tmplOpTypeIds = array();
			if(count($producOptionTypes))
			{
				foreach($producOptionTypes as $item)
				{
					$tmplOpTypeIds[] = $item->getOptionTypeId();
				}
			}else{
				$tmplOpTypeIds = array(0);
			}
				//get all is_deleted option_types 
			$prdOpTypeIds = Mage::getResourceModel('imageoption/optiontypesmap')->getPrdOpTypeIds($optionId,$tmplOpTypeIds);
				
				//is delete_action
			if(count($prdOpTypeIds))
			{
					//delete option_types
				$optionTypes = Mage::getModel('imageoption/optionvalue')->getCollection()
								->addFieldToFilter('option_type_id',array('in'=>$prdOpTypeIds));
				if(count($optionTypes))
				{
					foreach($optionTypes as $opType)
					{
						$opType->delete();
					}
				}
					//unmappping option_types
				$optiontypeMaps =  Mage::getModel('imageoption/optiontypesmap')->getCollection()
									->addFieldToFilter('product_option_type_id',array('in'=>$prdOpTypeIds));
				if(count($optiontypeMaps))
				{
					foreach($optiontypeMaps as $optiontypeMap)
					{
						$optiontypeMap->delete();
					}
				}
				//is add option_types
			} else {
			
				$tmplOpTypeIds =  Mage::getResourceModel('imageoption/optiontypesmap')->getTmplOpTypeIds($optionId);
				
				if(! count($tmplOpTypeIds))
				{
					$tmplOpTypeIds = array(0);
				}
				$optionValues = $optionvalue->getCollection()
									->addFieldToFilter('option_id',$optionId)
									->addFieldToFilter('option_type_id',array('nin'=>$tmplOpTypeIds));				
				if(count($optionValues))
				{
					$optionMaps = Mage::getModel('imageoption/optionsmap')->getCollection()
							->addFieldToFilter('template_option_id',$optionId);
					
					if(count($optionMaps))
					{
						foreach($optionMaps as $optionMap){}
						
						$imageoption =  Mage::getModel('imageoption/imageoption');
						$optiontypeMap =  Mage::getModel('imageoption/optiontypesmap');
						$resourceValue = Mage::getResourceModel('imageoption/optionvalue');
						
						foreach($optionValues as $optionValue)
						{
								//generate new product option_type
							$prdOpTypeId = $resourceValue->duplicateSelf($optionId,$optionMap->getProductOptionId(),$optionValue->getId());
							
								//mapping new option type product-template
							$optiontypeMap->setProductOptionTypeId($prdOpTypeId);
							$optiontypeMap->setTemplateOptionTypeId($optionValue->getId());
							$optiontypeMap->setOptionmapId($optionMap->getId());
							$optiontypeMap->save();
							$optiontypeMap->setId(null);
								//generate imageoption
							$imageoption->generateOptionTypeImage($optionId,$optionValue->getId(),$optionMap->getProductOptionId(),$prdOpTypeId,$this->getProductId());
						}
					}
				}
			}
		}
	}
}