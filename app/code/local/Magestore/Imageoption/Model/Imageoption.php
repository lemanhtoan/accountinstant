<?php

class Magestore_Imageoption_Model_Imageoption extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imageoption/imageoption');
    }
	
	public function duplicate($currProductId,$newProductId)
	{
		$collection = Mage::getResourceModel('imageoption/imageoption_collection')
					->addFieldToFilter('product_id',$currProductId);
		
		if(! count($collection))
			return;
			
		foreach($collection as $item)
		{
			$item->setData('product_id',$newProductId);
			$item->setId(null);
			$item->save();
		}
	}
	
	public function generateOptionImage($currOption,$newoptionId,$productId)
	{
		$imageOptions = Mage::getModel('imageoption/imageoption')->getCollection()
							->addFieldToFilter('option_id',$currOption->getId());
							
		if(! count($imageOptions))
			return;
			
		$optiontypeMap = Mage::getModel('imageoption/Optiontypesmap');	
		
		try{
		foreach($imageOptions as $imageoption)
		{		
			$newoptionTypeId = $optiontypeMap->getPrdOpTypeIdByTmplOpTypeId($imageoption->getOptionTypeId());
							
				//generate images
			$sourceimage = Mage::helper('imageoption/template')
							->getImagePath($imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getImage();
			$sourceimagecache = Mage::helper('imageoption/template')
							->getImagePathCache($imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getImage();
				
			$destimage = Mage::helper('imageoption')
							->getImagePath($productId,$newoptionId,$newoptionTypeId) . DS . $imageoption->getImage();
			$destimagecache = Mage::helper('imageoption')
							->getImagePathCache($productId,$newoptionId,$newoptionTypeId) . DS . $imageoption->getImage();
			
			Mage::helper('imageoption')->createImageFolder($productId,$newoptionId,$newoptionTypeId);
			
			//var_dump($sourceimage);die();
			
			if(file_exists($sourceimage))
			{
				copy($sourceimage,$destimage);
			}
			if(file_exists($sourceimagecache))
			{			
				copy($sourceimagecache,$destimagecache);
			}
				//generate option
			$imageoption->setData('product_id',$productId);
			$imageoption->setData('option_id',$newoptionId);
			$imageoption->setData('option_type_id',$newoptionTypeId);
			$imageoption->setData('is_template',0);
			$imageoption->setId(null);
			$imageoption->save();
		}
		
		} catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}
	}
	
	public function generateOptionTypeImage($oldOpId,$oldOpTypeId,$newOpId,$newOpTypeId,$productId)
	{
		$imageOptions = Mage::getModel('imageoption/imageoption')->getCollection()
							->addFieldToFilter('option_id',$oldOpId)
							->addFieldToFilter('option_type_id',$oldOpTypeId)
							->addFieldToFilter('product_id',0);
							
		if(! count($imageOptions))
			return;
		
		try{
		foreach($imageOptions as $imageoption)
		{		
				//generate images
			$sourceimage = Mage::helper('imageoption/template')
							->getImagePath($oldOpId,$oldOpTypeId) . DS . $imageoption->getImage();
			$sourceimagecache = Mage::helper('imageoption/template')
							->getImagePathCache($oldOpId,$oldOpTypeId) . DS . $imageoption->getImage();
				
			$destimage = Mage::helper('imageoption')
							->getImagePath($productId,$newOpId,$newOpTypeId) . DS . $imageoption->getImage();
			$destimagecache = Mage::helper('imageoption')
							->getImagePathCache($productId,$newOpId,$newOpTypeId) . DS . $imageoption->getImage();
			
			Mage::helper('imageoption')->createImageFolder($productId,$newOpId,$newOpTypeId);
			
			//var_dump($sourceimage);die();
			
			if(file_exists($sourceimage))
			{
				copy($sourceimage,$destimage);
			}
			if(file_exists($sourceimagecache))
			{			
				copy($sourceimagecache,$destimagecache);
			}
				//generate option
			$imageoption->setData('product_id',$productId);
			$imageoption->setData('option_id',$newOpId);
			$imageoption->setData('option_type_id',$newOpTypeId);
			$imageoption->setId(null);
			$imageoption->save();
		}
		
		} catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
		}	
	}
	
}