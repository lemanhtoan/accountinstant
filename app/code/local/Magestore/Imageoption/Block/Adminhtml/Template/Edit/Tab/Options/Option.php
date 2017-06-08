<?php

class Magestore_Imageoption_Block_Adminhtml_Template_Edit_Tab_Options_Option extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options_Option
{
	public function __construct()
	{
		return parent::__construct();
	}
    
	public function getProduct()
    {
        if (!$this->_productInstance) {
            if ($product = Mage::registry('product')) {
                $this->_productInstance = $product;
            } else {
                $product = Mage::getSingleton('catalog/product');
                $product->setId(0);
                $product->setStoreId(0);
                $this->_productInstance = $product;
            }
        }
        return $this->_productInstance;
    }

    public function getOptionValues()
    {
		
		$template = Mage::getModel('imageoption/template')
						->setId($this->getRequest()->getParam('id'));
		
		$template->setStoreId($this->getRequest()->getParam('store'));
		
		$options = $template->getOptions();
		
		if(count($options))
		{
			foreach ($options as $option)
			{
				$listOption[$option->getId()] = $option;
			}
        } else {
			$listOption = array();
		}		
		
		$optionsArr = array_reverse($listOption, true);

        if (!$this->_values) {
            $values = array();
            $scope = (int) Mage::app()->getStore()->getConfig(Mage_Core_Model_Store::XML_PATH_PRICE_SCOPE);
            foreach ($optionsArr as $option) {
                /* @var $option Mage_Catalog_Model_Product_Option */

                $this->setItemCount($option->getOptionId());

                $value = array();

                $value['id'] = $option->getOptionId();
                $value['item_count'] = $this->getItemCount();
                $value['option_id'] = $option->getOptionId();
                $value['title'] = $this->htmlEscape($option->getTitle());
                $value['type'] = $option->getType();
                $value['is_require'] = $option->getIsRequire();
                $value['sort_order'] = $option->getSortOrder();

                if ($this->getProduct()->getStoreId() != '0') {
                    $value['checkboxScopeTitle'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'title', is_null($option->getStoreTitle()));
                    $value['scopeTitleDisabled'] = is_null($option->getStoreTitle())?'disabled':null;
                }

                if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {

                    $i = 0;
                    $itemCount = 0;
                    foreach ($option->getValues() as $_value) {
                        /* @var $_value Mage_Catalog_Model_Product_Option_Value */
                        $value['optionValues'][$i] = array(
                            'item_count' => max($itemCount, $_value->getOptionTypeId()),
                            'option_id' => $_value->getOptionId(),
                            'option_type_id' => $_value->getOptionTypeId(),
                            'title' => $this->htmlEscape($_value->getTitle()),
                            'price' => $this->getPriceValue($_value->getPrice(), $_value->getPriceType()),
                            'price_type' => $_value->getPriceType(),
                            'sku' => $this->htmlEscape($_value->getSku()),
                            'sort_order' => $_value->getSortOrder(),
							'inputfile' => Mage::helper('imageoption/template')->getInputFileHtml($_value->getOptionTypeId()),
							'image_width' => Mage::helper('imageoption/template')->getWidthInputHtml($_value->getOptionTypeId()),							
                        );

                        if ($this->getProduct()->getStoreId() != '0') {
                            $value['optionValues'][$i]['checkboxScopeTitle'] = $this->getCheckboxScopeHtml($_value->getOptionId(), 'title', is_null($_value->getStoreTitle()), $_value->getOptionTypeId());
                            $value['optionValues'][$i]['scopeTitleDisabled'] = is_null($_value->getStoreTitle())?'disabled':null;
                            if ($scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                                $value['optionValues'][$i]['checkboxScopePrice'] = $this->getCheckboxScopeHtml($_value->getOptionId(), 'price', is_null($_value->getstorePrice()), $_value->getOptionTypeId());
                                $value['optionValues'][$i]['scopePriceDisabled'] = is_null($_value->getStorePrice())?'disabled':null;
                            }
                        }
                        $i++;
                    }
                } else {
                    $value['price'] = $this->getPriceValue($option->getPrice(), $option->getPriceType());
                    $value['price_type'] = $option->getPriceType();
                    $value['sku'] = $this->htmlEscape($option->getSku());
                    $value['max_characters'] = $option->getMaxCharacters();
                    $value['file_extension'] = $option->getFileExtension();
                    $value['image_size_x'] = $option->getImageSizeX();
                    $value['image_size_y'] = $option->getImageSizeY();
                    if ($this->getProduct()->getStoreId() != '0' && $scope == Mage_Core_Model_Store::PRICE_SCOPE_WEBSITE) {
                        $value['checkboxScopePrice'] = $this->getCheckboxScopeHtml($option->getOptionId(), 'price', is_null($option->getStorePrice()));
                        $value['scopePriceDisabled'] = is_null($option->getStorePrice())?'disabled':null;
                    }
                }
                $values[] = new Varien_Object($value);
            }
            $this->_values = $values;
        }

        return $this->_values;
    }

}
