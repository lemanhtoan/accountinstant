<?php

class Magestore_Imageoption_Model_Product_Option extends Mage_Catalog_Model_Product_Option {

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
				
				$imageNames[$option_type_id] = Mage::helper('imageoption')->uploadImageFile($product,$this->getData('option_id'),$option_type_id,$inputfile,$imageWidth);	
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
							Mage::helper('imageoption')->deleteOldImage($model_imageoption,$imageoption_data['image']);
						} else {
							Mage::helper('imageoption')->resizeImageOption($model_imageoption,$imageoption_data['image_width']);
						}
					}
			//	}
					$imageoption_data['product_id'] = $product->getId();
					$imageoption_data['option_id'] = $this->getData('option_id');
					$imageoption_data['option_type_id'] = $option_type_id;
					$imageoption_data['is_template'] = 0;
					
					$model_imageoption->setData($imageoption_data);
					
					$imageoption_data = null;
					
					if($imageoption_id)
					{
						$model_imageoption->setId($imageoption_id);
					}
					
					//var_dump($model_imageoption->getData());
					
					if($do_save)
					{
						try{
							$model_imageoption->save();
						} catch(Exception $e) {
						}
					}
			}
		//	die();

        } elseif ($this->getGroupByType($this->getType()) == self::OPTION_GROUP_SELECT) {
		
            Mage::throwException(Mage::helper('catalog')->__('Select type options required values rows.'));
		}
		
		return $return;
    }

}
?>