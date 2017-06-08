<?php

class Magestore_Imageoption_Model_Optionvalue extends Mage_Catalog_Model_Product_Option_Value 
{    
    public function duplicate($oldOptionId, $newOptionId)
    {
        $valueCond = Mage::getResourceModel('imageoption/optionvalue')->duplicate($this, $oldOptionId, $newOptionId);
        return $valueCond;
    }
	
	public function delete()
	{
		if(! $this->getId())
			return;
			
		$imageoptions = Mage::getModel('imageoption/imageoption')->getCollection()
							->addFieldToFilter('option_type_id',$this->getId());
							
		if(count($imageoptions))
		foreach($imageoptions as $imageoption)
		{
			Mage::helper('imageoption')->deleteOldImage($imageoption);
			//$imageoption->delete();
		}

		return parent::delete();
	}
}
?>