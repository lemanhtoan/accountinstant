<?php

class Magestore_Imageoption_Model_Productoption extends Mage_Catalog_Model_Product_Option 
{    
	public function saveOptions()
    {
		$optionsIds = array();
		
		foreach ($this->getOptions() as $option) {
            $this->setData($option)
                ->setData('product_id',0)
                ->setData('store_id', $this->getProduct()->getStoreId());

            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }
            $isEdit = (bool)$this->getId()? true:false;

            if ($this->getData('is_delete') == '1') {
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    $this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {
                    $previousType = $this->getData('previous_type');
                    //if previous option has dfferent group from one is came now need to remove all data of previous group
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {

                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }
                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                $this->setData('image_size_x', '0');
                                $this->setData('image_size_y', '0');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                        }
                        if ($this->getGroupByType($this->getData('type')) == self::OPTION_GROUP_SELECT) {
                            $this->setData('sku', '');
                            $this->unsetData('price');
                            $this->unsetData('price_type');
                            if ($isEdit) {
                                $this->deletePrices($this->getId());
                            }
                        }
                    }
                }
                $this->save();
				
				$optionsIds[] = $this->getId();
			}
        }//eof foreach()
        return $optionsIds;
    }
	
	
	protected function _afterSave()
    {	
		$return = null;
		
		$options = $this->getOptions();
		
		$product = $this->getProduct();
		
		$this->getValueInstance()->unsetValues();
		
        if (is_array($this->getData('values'))) 
		{
          
			foreach ($this->getData('values') as $value) 
			{				
				$option_type_id = $value['option_type_id'];

				$inputfile = 'productimage'. $option_type_id;
				
				//get image width, default 60 px
				if(isset($_POST['image_width_'.$option_type_id]))
					$imageWidth = $_POST['image_width_'.$option_type_id];				

				$imageWidth = isset($imageWidth) ? $imageWidth : 60;
				
				$imageNames[$option_type_id] = Mage::helper('imageoption/template')->uploadImageFile($this->getOptionId(),$option_type_id,$inputfile,$imageWidth);	
			}
            	
			$return = parent::_afterSave();
				
			
			// get option_type_value after save
			$values = Mage::getResourceModel('catalog/product_option_value_collection')
					->addOptionToFilter($this->getData('option_id'));
			
			
			foreach($values as $value)
			{	
				$option_type_id = $value->getData('option_type_id');	
					//get image width
				if(isset($_POST['image_width_'.$option_type_id]))
					$imageoption_data['image_width'] = $_POST['image_width_'.$option_type_id];				
				
				$imageoption_data['image_width'] = isset($imageoption_data['image_width']) ? $imageoption_data['image_width'] : 60;
	
				$model_imageoption = Mage::getModel('imageoption/imageoption');		
				
				$imageoption_id = Mage::getResourceModel('imageoption/imageoption')->loadIdByTypeOptionId($option_type_id);
				
				$imageoption_id = intval($imageoption_id);				
				
				if(isset($imageNames[$option_type_id]) && $imageNames[$option_type_id])
				{
					$imageoption_data['image'] = $imageNames[$option_type_id];
				}	
				
				if(! $imageoption_id )
				{
						$model_imageoption = Mage::getModel('imageoption/imageoption');
						
						if(isset($imageoption_data['image']) && ( $imageoption_data['image'] != ''))
						{
							$do_save = true;
						} else{
							$do_save = false;
						}
				} else {
									
						$model_imageoption = Mage::getModel('imageoption/imageoption')->load($imageoption_id);
						
						$do_save = true;
						
						if(isset($imageoption_data['image'] ))
						{
							Mage::helper('imageoption/template')->deleteOldImage($model_imageoption);
						} else {
		
							Mage::helper('imageoption/template')->resizeImageOption($model_imageoption,$imageoption_data['image_width']);
						}
					}
		
					$imageoption_data['product_id'] = $product->getId();
					$imageoption_data['option_id'] = $this->getData('option_id');
					$imageoption_data['option_type_id'] = $option_type_id;
					$imageoption_data['is_template'] = 1;
					
					$model_imageoption->setData($imageoption_data);
					
					$imageoption_data = null;
					
					if($imageoption_id)
					{
						$model_imageoption->setId($imageoption_id);
					}
					
					if($do_save)
					{
						try{
							$model_imageoption->save();
						} catch(Exception $e) {
						}
					}
			}

        } elseif ($this->getGroupByType($this->getType()) == self::OPTION_GROUP_SELECT) {
		
            Mage::throwException(Mage::helper('catalog')->__('Select type options required values rows.'));
		}
		
		return $return;
    }
	
	public function delete()
	{
		if(! $this->getId())
			return;
		/*	
		$collection = Mage::getModel('imageoption/imageoption')->getCollection()
							->addFieldToFilter('option_id',$this->getId());
							
		if(count($collection))
		foreach($collection as $imageoption)
		{
			Mage::helper('imageoption')->deleteOldImage($imageoption);
			//$imageoption->delete();
		}
		*/
		return parent::delete();
	}
	
	public function duplicate($oldProductId, $newProductId)
    {
       return Mage::getResourceModel('imageoption/productoption')->duplicate($this, $oldProductId, $newProductId);
    }

}
?>