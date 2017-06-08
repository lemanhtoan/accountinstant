<?php

class Magestore_Imageoption_Model_Template extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imageoption/template');
    }
	
	public function getOptionIds()
	{	
		$collection = Mage::getModel('imageoption/optiontemplate')->getCollection()
						->addFieldToFilter('template_id',$this->getId());
		
		if(! count($collection))
			return;
			
		$ids = array();
		foreach($collection as $item)
		{
			$ids[] = $item->getOptionId();
		}
		
		return $ids;
	}
	
	public function getOptions()
	{
		$optionIds = $this->getOptionIds();
		
		if(! count($optionIds))
			return array();
     	
		$option = Mage::getSingleton('catalog/product_option'); 	
	
        $options = $option->getCollection()
            ->addFieldToFilter('product_id',0)
            ->addFieldToFilter('main_table.option_id',array('in'=>$optionIds))
            ->addTitleToResult($this->getStoreId())
            ->addPriceToResult($this->getStoreId())
            ->setOrder('sort_order', 'asc')
            ->setOrder('title', 'asc')
            ->addValuesToResult($this->getStoreId()); 
		
		return $options;
	}
	
	public function getProductIds()
	{
		$ids = array();
		
		if(! $this->getId())
			return $ids;
			
		$collection = Mage::getModel('imageoption/producttemplate')->getCollection()
						->addFieldToFilter('template_id',$this->getId());
						
		if(! count($collection))
			return $ids;
			
		foreach($collection as $item)
		{
			$ids[] = $item->getProductId();
		}
		
		return $ids;						
	}
	
	public function getTmplByPrdId($productId)
	{
		$productTemplates = Mage::getModel('imageoption/producttemplate')->getCollection()
								->addFieldToFilter('product_id',$productId);
		
		if( !count($productTemplates))
			return;
			
		$tmplIds = array();	
		
		foreach($productTemplates as $item)
		{
			$tmplIds[] = $item->getTemplateId();
		}
		
		return $this->getCollection()->addFieldToFilter('template_id',array('in'=>$tmplIds))
									 ->addFieldToFilter('status',1);
	}
	
	public function delete()
	{
		if(! $this->getId())
			return;
			
			// unassigne products,
		$productTemplates = Mage::getModel('imageoption/producttemplate')->getCollection()
								->addFieldToFilter('template_id',$this->getId())
								//->addFieldToFilter('status',1)
								;
		
		if(count($productTemplates))
		{
			foreach($productTemplates as $producttemplate)
			{
				//$producttemplate->unassignProduct();
				$producttemplate->delete();
			}
		}
			//remove template option image
		$templateOptions = Mage::getModel('imageoption/optiontemplate')->getCollection()
							->addFieldToFilter('template_id',$this->getId());
		if(count($templateOptions))
		{						
			$optionIds = array();
			foreach($templateOptions as $templateoption)
			{
				$optionIds[] = $templateoption->getOptionId();
			}
			$imageOptions = Mage::getModel('imageoption/imageoption')->getCollection()
							->addFieldToFilter('option_id',array('in'=>$optionIds));
			if(count($imageOptions))
			foreach($imageOptions as $imageoption)
			{
				Mage::helper('imageoption/template')->deleteOldImage($imageoption);
				$imageoption->delete();
			}
		}
		return parent::delete();
	}
}