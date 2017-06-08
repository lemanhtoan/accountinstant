<?php

class Magestore_Imageoption_Block_Select
    extends Mage_Catalog_Block_Product_View_Options_Type_Select
{

    public function getValuesHtml()
    {
        $_option = $this->getOption();

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN
            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
            $require = ($_option->getIsRequire()) ? ' required-entry' : '';
            $extraParams = '';
            $select = $this->getLayout()->createBlock('core/html_select')
                ->setData(array(
                    'id' => 'select_'.$_option->getId(),
                    'class' => $require.' product-custom-option'
                ));
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_DROP_DOWN) {
                $select->setName('options['.$_option->getid().']')
                    ->addOption('', $this->__('-- Please Select --'));
            } else {
                $select->setName('options['.$_option->getid().'][]');
                $select->setClass('multiselect'.$require.' product-custom-option');
            }
            foreach ($_option->getValues() as $_value) {
                $priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent') ? true : false,
                    'pricing_value' => $_value->getPrice(true)
                ), false);
                $select->addOption(
                    $_value->getOptionTypeId(),
                    $_value->getTitle() . ' ' . $priceStr . ''
                );
            }
            if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_MULTIPLE) {
                $extraParams = ' multiple="multiple"';
            }
            $select->setExtraParams('onchange="opConfig.reloadPrice()"'.$extraParams);

            return $select->getHtml();
        }

        if ($_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO
            || $_option->getType() == Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX
            ) {
            //$selectHtml = '<ul id="options-'.$_option->getId().'-list" class="options-list">';
            $selectHtml = '';
			$require = ($_option->getIsRequire()) ? ' validate-one-required-by-name' : '';
            $arraySign = '';
            switch ($_option->getType()) {
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_RADIO:
                    $type = 'radio';
                    $class = 'radio';
                    if (!$_option->getIsRequire()) {
                       $selectHtml .= '<input type="radio" id="options_'.$_option->getId().'" class="'.$class.' product-custom-option" name="options['.$_option->getId().']" onclick="opConfig.reloadPrice()" value="" checked="checked" /><span class="label"><label for="options_'.$_option->getId().'">' . $this->__('None') . '</label></span>';
                    }
                    break;
                case Mage_Catalog_Model_Product_Option::OPTION_TYPE_CHECKBOX:
                    $type = 'checkbox';
                    $class = 'checkbox';
                    $arraySign = '[]';
                    break;
            }
            $count = 1;
			
			$displaytype = Mage::getStoreConfig('imageoption/general/displaytype');
			
		if($displaytype) //horizinal
		{
				///refine check valide
			$html ='<input type="radio" value="1" name="option_valid_'. $_option->getId() .'" id="radio_'. $_option->getId() .'" class="'.$class.' '.$require.' product-custom-option hidden-option" >';
			
			//$html .= '<div  style="display:none;" class="overviewoption" id="overviewoption'. $_option->getId() .'"> &nbsp;</div>';
					
			foreach ($_option->getValues() as $_value) {
                $count++;
                $priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent') ? true : false,
                    'pricing_value' => $_value->getPrice(true)
                ));

			
			$inputHtml = '<span class="label"><label for="options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle() .'</label></span>';
			if($priceStr)
				$inputHtml .='<br/>'.$priceStr;
			$inputHtml .= '<br/><input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option" onclick="opConfig.reloadPrice()" name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId() .'_'. $count .'" value="'.$_value->getOptionTypeId().'" />';
			
				//refine check valide
			$html .= '<style>#advice-validate-one-required-by-name-options_'. $_option->getId() .'_'. $count .' {display:none;}</style>';
				
				//get MenuImage				   
			$imageoption_id = Mage::getResourceModel('imageoption/imageoption')->loadIdByTypeOptionId($_value->getData('option_type_id'));
			
			$width_image = 0;
			
			if($imageoption_id)
			{
				$imageoption = Mage::getModel('imageoption/imageoption')->load($imageoption_id);
				
				if(!Mage::helper('imageoption')->is_existedImage($imageoption))
					return parent::getValuesHtml();				
				
				$productId= $imageoption->getData('product_id');
				
				$url_img = Mage::helper('imageoption')->getImageUrl($imageoption,$_option->getProduct()->getId());
			
				$width_image = $imageoption->getData('image_width');
			
			} else {
				$url_img = '';
				return parent::getValuesHtml();
			}
				$width_image = $width_image ? $width_image : 60;
				$option_id = $_option->getId();
				$option_info = $_option->getTitle() .': '. $_value->getTitle();
				$html .= '<div style="float:left;" class="bound-menu-image" style="width:'. $width_image .'px;" >';
				if($url_img != '')
				{
					$html .= '<div name="div_menu_image" class="div-menu-image" id="div_image_menu_'. $_value->getData('option_type_id') .'">'
						.'<img title="'. $option_info .'" class="menu-image"  width="'. $width_image .'" id="menu_image_'. $_value->getData('option_type_id') .'" name="imageoption"'  
						 .' onmouseout="hiddenOverview(\''. $option_id .'\');" onmouseover="overviewOption(this,\''. $option_id .'\',\''. $option_info .'\');" onclick="sameReloadPrice(\''. $option_id .'\',\''.$count.'\');" src="'. $url_img .'">'
						 .'</div>';
				} else {
					$html .= '<div  style="display:none;" class="div-menu-image" id="div_image_menu_'. $_value->getData('option_type_id') .'">'
							.'<img  style="display:none;" class="menu-image"  id="menu_image_'. $_value->getData('option_type_id') .'" >';
				    $html .= '</div>';					
				}	 
				$html .= $inputHtml;
				$html .= '</div>';
			// end get MenuImage
				
				if ($_option->getIsRequire()) {
                //    $html .= '<script type="text/javascript">' .
                                    '$(\'options_'.$_option->getId().'_'.$count.'\').advaiceContainer = \'options-'.$_option->getId().'-container\';' .
                                    '$(\'options_'.$_option->getId().'_'.$count.'\').callbackFunction = \'validateOptionsCallback\';' .
                                   '</script>';
                }
               // $selectHtml .= '</li>';
            }
		} else { //vertical
			
			$html = '<table cellpadding="0" cellspacing="0">';
			
			$html .='<input type="radio" value="1" name="option_valid_'. $_option->getId() .'" id="radio_'. $_option->getId() .'" class="'.$class.' '.$require.' product-custom-option hidden-option" >';
	
			foreach ($_option->getValues() as $_value) {
                $count++;
                $priceStr = $this->_formatPrice(array(
                    'is_percent' => ($_value->getPriceType() == 'percent') ? true : false,
                    'pricing_value' => $_value->getPrice(true)
                ));

			$inputHtml = ' <input type="'.$type.'" class="'.$class.' '.$require.' product-custom-option" onclick="opConfig.reloadPrice()" name="options['.$_option->getId().']'.$arraySign.'" id="options_'.$_option->getId() .'_'. $count .'" value="'.$_value->getOptionTypeId().'" />';
			$labelHtml = '<span class="label"><label for="options_'.$_option->getId().'_'.$count.'">'.$_value->getTitle() .'</label></span>';
			if($priceStr)
				$labelHtml .=' '.$priceStr;
			
				//refine check valide
			$html .= '<style>#advice-validate-one-required-by-name-options_'. $_option->getId() .'_'. $count .' {display:none;}</style>';
				
				//get MenuImage				   
			$imageoption_id = Mage::getResourceModel('imageoption/imageoption')->loadIdByTypeOptionId($_value->getData('option_type_id'));
			
			$width_image = 0;
			
			if($imageoption_id)
			{
				$imageoption = Mage::getModel('imageoption/imageoption')->load($imageoption_id);
				
				if(!Mage::helper('imageoption')->is_existedImage($imageoption))
					return parent::getValuesHtml();				
				
				$productId= $imageoption->getData('product_id');
				
				$url_img = Mage::helper('imageoption')->getImageUrl($imageoption,$_option->getProduct()->getId());
			
				$width_image = $imageoption->getData('image_width');
			
			} else {
				$url_img = '';
				return parent::getValuesHtml();				
			}
				$width_image = $width_image ? $width_image : 60;
				$option_id = $_option->getId();
				$option_info = $_option->getTitle() .': '. $_value->getTitle();
				$html .= '<tr>';
				$html .= '<td style="vertical-align:middle;">';				
				$html .= $inputHtml;	
				$html .='</td>';
				if($url_img != '')
				{
					$html .='<td align="center">';
					$html .= '<div class="bound-menu-image">';
					$html .= '<div name="div_menu_image" class="div-menu-image" id="div_image_menu_'. $_value->getData('option_type_id') .'">'
						.'<img style="margin:0 10px 0 10px;" title="'. $option_info .'" class="menu-image"  width="'. $width_image .'" id="menu_image_'. $_value->getData('option_type_id') .'" name="imageoption"'  
						 .' onmouseout="hiddenOverview(\''. $option_id .'\');" onmouseover="overviewOption(this,\''. $option_id .'\',\''. $option_info .'\');" onclick="sameReloadPrice(\''. $option_id .'\',\''.$count.'\');" src="'. $url_img .'">'
						 .'</div>';
					$html .= '</div>';				
					$html .= '</td>';						 
				} else {
					$html .='<td style="display:none;" align="center">';
					$html .= '<div style="display:none;" class="bound-menu-image">';				
					$html .= '<div style="display:none;" class="div-menu-image" id="div_image_menu_'. $_value->getData('option_type_id') .'">'
							.'<img  style="display:none;" class="menu-image"  id="menu_image_'. $_value->getData('option_type_id') .'" >';
				    $html .= '</div>';	
					$html .= '</div>';				
					$html .= '</td>';					
				}
				$html .= '<td style="vertical-align:middle;">';
				$html .= $labelHtml;
				$html .= '</td>';
				$html .= '</tr>';
            }	
			$html .= '</table>';
		}		
			$html .= '<div class="fix">&nbsp;</div>';
            $selectHtml .= '<div class="fix">&nbsp;</div>';
			$selectHtml .= $html;
            return $selectHtml;
        }
    }

}