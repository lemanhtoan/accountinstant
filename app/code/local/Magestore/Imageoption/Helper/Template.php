<?php

class Magestore_Imageoption_Helper_Template extends Mage_Core_Helper_Abstract
{

	public function getImageHeight($width,$imageObject)
	{
		$org_width = $imageObject->getOriginalWidth();
		$org_height = $imageObject->getOriginalHeight();
		
		return (int) $org_height * $width / $org_width;
	}
	
	public function uploadImageFile($option_id,$option_type_id,$inputfile,$width)
	{	
		if(! isset($_FILES[$inputfile]))
		{
			return;
		}
		
		$this->createImageFolder($option_id,$option_type_id);
		
		$imageName = "";
		$newImageName = "";
		
		$image_path = $this->getImagePath($option_id,$option_type_id);
		$image_path_cache = $this->getImagePathCache($option_id,$option_type_id); 
		
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

				$newImageName = 'n_'. $this->refineString($imageName);					
				
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
			
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
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
		
		$cacheImagePath = $this->getImagePathCache($imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getImage();
		
		$imagePath = $this->getImagePath($imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getImage();
		
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
	
	public function deleteOldImage($imageoption)
	{
		$image_path = $this->getImagePath($imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getData('image');
		
		$image_path_cache = $this->getImagePathCache($imageoption->getOptionId(),$imageoption->getOptionTypeId()) . DS . $imageoption->getData('image');

		try{
			if(file_exists($image_path))
			{
				unlink($image_path);
			}
			if(file_exists($image_path_cache))
			{
				unlink($image_path_cache);
			}			
		} catch(Exception $e) {
		
		}
	}
	
	public function createImageFolder($option_id,$option_type_id)
	{
		$imageoption_path = Mage::getBaseDir('media') . DS .'imageoptions';
		$imageoption_path_template = Mage::getBaseDir('media') . DS .'imageoptions' . DS .'template';
		$path_cache = $imageoption_path_template . DS .'cache';
		
		$image_path = $this->getImagePath($option_id,$option_type_id);
		$image_path_cache = $this->getImagePathCache($option_id,$option_type_id); 

		if(!is_dir($imageoption_path))
		{
			try{
			
				chmod(Mage::getBaseDir('media'),0755);
				
				mkdir($imageoption_path);
				
				chmod($imageoption_path,0755);
				
			} catch(Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() .' : '. $imageoption_path);
			}
		}		
		
		if(!is_dir($imageoption_path_template))
		{
			try{
			
				chmod($imageoption_path,0755);
				
				mkdir($imageoption_path_template);
				
				chmod($imageoption_path_template,0755);
				
			} catch(Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() .' : '. $imageoption_path);
			}
		}

		if(!is_dir($path_cache))
		{
			try{
			
				chmod($imageoption_path_template,0755);
				
				mkdir($path_cache);
				
				chmod($path_cache,0755);
				
			} catch(Exception $e) {	
				Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() .' : '. $path_cache);
			}		
		}		
		
		if(!is_dir($image_path))
		{
			try{
				chmod($imageoption_path_template,0755);
				
				mkdir($image_path);
				
				chmod($image_path,0755);
			
			} catch(Exception $e) {	
				Mage::getSingleton('adminhtml/session')->addError( $e->getMessage() .' : '. $image_path);
			}
		}
		
		if(!is_dir($image_path_cache))
		{
			try{
				chmod($path_cache,0755);
			
				mkdir($image_path_cache);
			
				chmod($image_path_cache,0755);
				
			} catch(Exception $e) {		
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage() .' : '. $image_path_cache);
			
			}
		}		
	}
	
	
	public function getImagePath($option_id,$option_type_id)
	{
		$image_path = Mage::getBaseDir('media') . DS .'imageoptions' . DS . 'template' . DS . $option_id . '_' . $option_type_id;
		
		return $image_path;		
	}
	
	public function getImagePathCache($option_id,$option_type_id)
	{
		$image_path_cache = Mage::getBaseDir('media') . DS .'imageoptions' . DS . 'template' .DS. 'cache' . DS . $option_id . '_' . $option_type_id;
		
		return $image_path_cache;		
	}

	
	public function getInputFileHtml($option_type_id)
	{
		$html = '';
		
			$imageoptions = Mage::getResourceModel('imageoption/imageoption_collection')
				->addFieldToFilter('option_type_id',$option_type_id)
				->addFieldToFilter('product_id',0);
			
			$image_url = '';
			if(count($imageoptions))
			{
				foreach($imageoptions as $imageoption){}
				
				$image_url = $this->getImageUrl($imageoption);
			}
			if($image_url)
				$disabled = 'disabled';
			else $disabled = '';
			
			$html ='<input size="5" class="input-text" type="file" '.$disabled.' name="productimage'. $option_type_id .'" >';
		
			if($image_url)
			{
				$html .= '<br><img src="'. $image_url .'" width="50px" >';
			}
		
		
		return $html;
	}
	
	public function getWidthInputHtml($option_type_id)
	{
		$html = '';
		

			$imageoptions = Mage::getResourceModel('imageoption/imageoption_collection')
				->addFieldToFilter('option_type_id',$option_type_id)
				->addFieldToFilter('product_id',0);
			
			$image_width = 0;
			if(count($imageoptions))
			{
				foreach($imageoptions as $imageoption){}
				$image_width = $imageoption->getData('image_width');
			}
			
			$image_width = $image_width ? $image_width : 60;
			
			$html .= '<input type="text" name="image_width_'. $option_type_id .'" size="5" value="'. $image_width .'" onclick="enableInputFile('.$option_type_id.')" > px';

	
		
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
	
	public function getImageUrl($imageoption)
	{
		
		return Mage::getBaseUrl('media') .'imageoptions/template/cache/'. $imageoption->getOptionId().'_'. $imageoption->getOptionTypeId() .'/'. $imageoption->getData('image');
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
}