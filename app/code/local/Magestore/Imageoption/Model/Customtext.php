<?php

class Magestore_Imageoption_Model_Customtext extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('imageoption/customtext');
    }
	
	public function loadByOptionId($option_id)
	{
		return $this->getCollection()
					->addFieldToFilter('option_id',$option_id)
					->getFirstItem();
	}
	
	public function duplicate($currProduct,$newProduct)
	{
		$currOptions = $currProduct->getProductOptionsCollection();
		if(count($currOptions)){
			foreach($currOptions as $currOption){
				$customText = $this->loadByOptionId($currOption->getId());
				if($customText->getId()){
					$newOption = $this->getOptionByTitlePrd($currProduct->getId(),$currOption->getTitle());
					if($newOption->getId()){
						$newCustomText = $this->loadByOptionId($newOption->getId())
												->setProductId($newProduct->getId())
												->setOptionId($newOption->getId())
												->setCustomText($customText->getCustomText())
												->save();
					}
				}
			}
		}
	}
	
	public function getOptionByTitlePrd($product_id,$title)
	{
		$option = Mage::getResourceModel('catalog/product_option_collection')
						->addFieldToFilter('product_id',$product_id)
						->addTitleToResult()
						->addFieldToFilter('`default_option_title`.`title`',$title)
						->getFirstItem();
		return $option;
	}
}