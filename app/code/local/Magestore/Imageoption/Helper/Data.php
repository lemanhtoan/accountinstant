<?php

class Magestore_Imageoption_Helper_Data extends Mage_Core_Helper_Abstract
{

	public function getImageHeight($width,$imageObject)
	{
		$org_width = $imageObject->getOriginalWidth();
		$org_height = $imageObject->getOriginalHeight();
		
		return (int) $org_height * $width / $org_width;
	}

	public function uploadImageFile($product,$option_id,$option_type_id,$inputfile,$width)
	{	
		if(! isset($_FILES[$inputfile]))
		{
			return;
		}
		
		$this->createImageFolder($product->getId(),$option_id,$option_type_id);
		
		$imageName = "";
		$newImageName = "";
		
		$image_path = $this->getImagePath($product->getId(),$option_id,$option_type_id);
		$image_path_cache = $this->getImagePathCache($product->getId(),$option_id,$option_type_id); 
		
		$file = $_FILES[$inputfile];
		
		if(isset($file['name']) && ($file['name'] != '')) {
		try {	
				$imageName = $file['name'];
				/* Starting upload */	
				$uploader = new Varien_File_Uploader($inputfile);
				
				// Any extention would work
				$uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
				$uploader->setAllowRenameFiles(true);
									
				$uploader->setFilesDispersion(false);

				$newImageName = substr(md5($imageName),0,10) . $this->refineString($imageName);					
				
				$uploader->save($image_path, $imageName);
				
				$fileImg = new Varien_Image($image_path.DS.$imageName);
				$fileImg->keepAspectRatio(true);
				$fileImg->keepFrame(true);
				$fileImg->keepTransparency(true);
				$fileImg->constrainOnly(false);
				$fileImg->backgroundColor(array(255,255,255));
				
				$fileImg->resize(intval($width),$this->getImageHeight($width,$fileImg));
				$fileImg->save($image_path_cache.DS.$newImageName,null);				
				
				if($newImageName != $imageName)
				{
					copy($image_path .DS. $imageName,$image_path .DS.$newImageName);
				
					unlink($image_path.DS.$imageName);
				}		
				
			} catch (Exception $e) {
			
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() .  $e->getMessage());
			}
	        			
			$imageName = $newImageName;
		}
		return $imageName;
	}
	
	public function resizeImageOption($imageoption,$new_width)
	{
		if(!$imageoption)
			return;
		
		if(! $imageoption->getImage())
			return;
			
		if($imageoption->getImageWidth() == $new_width)
			return;
		
		$cacheImagePath = $this->getImagePathCache($imageoption->getProductId(),$imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getImage();
		$imagePath = $this->getImagePath($imageoption->getProductId(),$imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getImage();
			
		if(file_exists($imagePath))
		{
			$fileImg = new Varien_Image($imagePath);
			$fileImg->keepAspectRatio(true);
			$fileImg->keepFrame(true);
			$fileImg->keepTransparency(true);
			$fileImg->constrainOnly(false);
			$fileImg->backgroundColor(array(255,255,255));
				
			$fileImg->resize(intval($new_width),$this->getImageHeight($new_width,$fileImg));
			$fileImg->save($cacheImagePath,null);				
		}				
	}
	
	public function deleteOldImage($imageoption,$newimage=null)
	{
		$product = Mage::getModel('catalog/product')->load($imageoption->getData('product_id'));
		
		$image_path = $this->getImagePath($product->getId(),$imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getImage();
		$image_path_cache = $this->getImagePathCache($product->getId(),$imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getImage();
		try{
			if(file_exists($image_path))
			{
				if($imageoption->getImage()!=$newimage)
					unlink($image_path);
			}
			if(file_exists($image_path_cache))
			{
				if($imageoption->getImage()!=$newimage)
					unlink($image_path_cache);
			}			
		} catch(Exception $e) {
		
		}
	}
	
	public function createImageFolder($product_id,$option_id,$option_type_id)
	{
		$imageoption_path = Mage::getBaseDir('media') . DS .'imageoptions';
		$path_cache = Mage::getBaseDir('media') . DS .'imageoptions'. DS .'cache';
		
		$product_path = $imageoption_path . DS . $product_id;
		$product_path_cache = $path_cache . DS . $product_id;
		
		$image_path = $this->getImagePath($product_id,$option_id,$option_type_id);
		$image_path_cache = $this->getImagePathCache($product_id,$option_id,$option_type_id); 

		if(!is_dir($imageoption_path))
		{
			try{
			
				chmod(Mage::getBaseDir('media'),0755);
				
				mkdir($imageoption_path);
				
				chmod($imageoption_path,0755);
				
			} catch(Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			}
		}

		if(!is_dir($path_cache))
		{
			try{
			
				chmod($imageoption_path,0755);
				
				mkdir($path_cache);
				
				chmod($path_cache,0755);
				
			} catch(Exception $e) {	
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			}		
		}	
		
		if(!is_dir($product_path))
		{
			try{
			
				chmod($imageoption_path,0755);
				
				mkdir($product_path);
				
				chmod($product_path,0755);
				
			} catch(Exception $e) {	
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			}		
		}			

		if(!is_dir($product_path_cache))
		{
			try{
			
				chmod($path_cache,0755);
				
				mkdir($product_path_cache);
				
				chmod($product_path_cache,0755);
				
			} catch(Exception $e) {	
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			}		
		}		
		
		if(!is_dir($image_path))
		{
			try{
				chmod($imageoption_path,0755);
				
				mkdir($image_path);
				
				chmod($image_path,0755);
			
			} catch(Exception $e) {	
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			}
		}
		
		if(!is_dir($image_path_cache))
		{
			try{
				chmod($path_cache,0755);
			
				mkdir($image_path_cache);
			
				chmod($image_path_cache,0755);
				
			} catch(Exception $e) {		
				Mage::getSingleton('adminhtml/session')->addError($this->getErrorMessage() . $e->getMessage());
			
			}
		}		
	}
	
	public function getImagePath($productId,$option_id,$option_type_id)
	{	
		$image_path = Mage::getBaseDir('media') . DS .'imageoptions' . DS . $productId . DS . $option_id .'_'. $option_type_id;
		
		return $image_path;		
	}
	
	public function getImagePathCache($productId,$option_id,$option_type_id)
	{
		//$imageFolderName = $this->getImageFolderName($productName);		
		
		$image_path_cache = Mage::getBaseDir('media') . DS .'imageoptions' . DS .'cache'. DS . $productId . DS . $option_id .'_'. $option_type_id;
		
		return $image_path_cache;		
	}

	public function getImageFolderName($productName)
	{
		$productName = $this->refineString($productName);

		return strtolower(substr($productName,0,1)).substr(md5($productName),0,10). $productName;
	}
	
	public function getInputFileHtml($option_type_id,$product)
	{
		$html = '';
		
		$collection = Mage::getResourceModel('catalog/product_option_value_collection')
				->addFieldToFilter('option_type_id',$option_type_id);
		
		if(count($collection))
		{
			$imageoptions = Mage::getResourceModel('imageoption/imageoption_collection')
				->addFieldToFilter('option_type_id',$option_type_id);
			
			$image_url = '';
			if(count($imageoptions))
			{
				foreach($imageoptions as $imageoption){}
				
				$image_url = $this->getImageUrl($imageoption,$product->getId());
			}
			if($image_url) $disabled = 'disabled';
			else $disabled = '';
			
			$html ='<input size="5" class="input-text" type="file" '.$disabled.' name="productimage'. $option_type_id .'" >';
		
			if($image_url)
			{
				$html .= '<br><img src="'. $image_url .'" width="50px" >';
			}
		} 
		
		return $html;
	}
	
	public function getWidthInputHtml($option_type_id)
	{
		$html = '';
		
		$collection = Mage::getResourceModel('catalog/product_option_value_collection')
				->addFieldToFilter('option_type_id',$option_type_id);
		
		if(count($collection))
		{
			$imageoptions = Mage::getResourceModel('imageoption/imageoption_collection')
				->addFieldToFilter('option_type_id',$option_type_id);
			
			$image_width = 0;
			if(count($imageoptions))
			{
				foreach($imageoptions as $imageoption){}
				$image_width = $imageoption->getData('image_width');
			}
			
			$image_width = $image_width ? $image_width : 60;
			
			$html .= '<input type="text" name="image_width_'. $option_type_id .'" size="5" value="'. $image_width .'" onclick="enableInputFile('.$option_type_id.')" > px';

		} 
		
		return $html;		
	}
	
	public function getinputHiddenImageHtml($option_id)
	{
		$html = '';
		
		$collection = Mage::getResourceModel('imageoption/imageoption_collection')
					->addFieldToFilter('option_id',$option_id);
		
		if(count($collection))
		{
			foreach($collection as $item)
			{
				$productId = $item->getData('product_id');
				
				$url_img = Mage::getBaseUrl('media') .'imageoptions/'. $productId .'/'. $item->getData('image');
				
				$html .= '<input type="hidden" name="imageoption" id="image_'. $item->getData('option_type_id') .'" value="'. $url_img .'">';
			}
		}
		
		return $html;
	}
	
	public function getMenuImageHtml($option_id,$type,$last)
	{
		$border = $last ? '' : '<div class="border" >&nbsp;</div>' ;

		if(($type != 'drop_down') && ($type != 'multiple'))
		{	
			return $border;
		}
		
		$html = '';
		//$html .= '<div class="overviewoption" id="overviewoption'. $option_id .'"> &nbsp;</div>';
		
		$option_title = Mage::getResourceModel('imageoption/imageoption')->getOptionTitle($option_id);
		
		$values = Mage::getResourceModel('imageoption/imageoption')->getOptionType($option_id);
		
		$i=0;
		
		if(count($values))
		foreach($values as $_value)
		{
			$i++;

			$option_type_title = $_value['title'];
			
			$option_info = $option_title .': '.$option_type_title;
			
			$imageoption_id = Mage::getResourceModel('imageoption/imageoption')->loadIdByTypeOptionId($_value['option_type_id']);
			
			$width_image = 0;
			
			if($imageoption_id)
			{
				$imageoption = Mage::getModel('imageoption/imageoption')->load($imageoption_id);
				
				$productId = $imageoption->getData('product_id');
				
				$url_img = $this->getImageUrl($imageoption,$productId);
				
				$width_image = $imageoption->getData('image_width');
				
			} else {
			
				$url_img = '';
			}
				$width_image = $width_image ? $width_image : 60;
				
				$html .= '<div class="bound-menu-image">';
				if($url_img)
				{
					$html .= '<div name="div_menu_image" class="div-menu-image" id="div_image_menu_'. $_value['option_type_id'] .'">'
						.'<img title="'. $option_info .'" class="menu-image"  width="'. $width_image .'" id="menu_image_'. $_value['option_type_id'] .'" name="imageoption"'  
						 .' onmouseout="hiddenOverview(\''. $option_id .'\');" onmouseover="overviewOption(this,\''. $option_id .'\',\''. $option_info .'\');" onclick="sameReloadPrice(\''. $option_id .'\',\''.$_value['option_type_id'].'\');" src="'. $url_img .'">';
				
				    $html .= '</div>';
				} else {
					$html .= '<div style="display:none;" class="div-menu-image" id="div_image_menu_'. $_value['option_type_id'] .'">'
							.'<img  style="display:none;" class="menu-image"  id="menu_image_'. $_value['option_type_id'] .'" >';
				    $html .= '</div>';					
				}
				$html .= '</div>';
		}
		
		$html .= '<div class="fix">&nbsp;</div>';
		
		$html .= $border;
		
		return $html;		
	}
	
	public function getImageUrl($imageoption,$productId)
	{
		return Mage::getBaseUrl('media') .'imageoptions/cache/'. $productId .'/'. $imageoption->getOptionId() .'_'. $imageoption->getOptionTypeId() .'/'. $imageoption->getData('image');
	}
	
	public function refineString($str)
	{
		for($i=0;$i<5;$i++)
		{
			$str = str_replace("  "," ",$str);
		}
		$str = str_replace(" ","_",$str);
		$str = preg_replace('/(<\/?)(\w+)([^>]*>)/e','',$str);
		$str = preg_replace(array('/\'/','/"/'),'',$str);
		$str = strtolower($str);
		
		return $str;	
	}
	
	public function getErrorMessage()
	{
		return ;
	}
	
	public function is_existedImage($imageoption)
	{
		$path = $this->getImagePathCache($imageoption->getProductId(),$imageoption->getOptionId(),$imageoption->getOptionTypeId());
		$path .= DS. $imageoption->getImage();
		if(file_exists($path))
			return true;
		return false;
	}	
	
}