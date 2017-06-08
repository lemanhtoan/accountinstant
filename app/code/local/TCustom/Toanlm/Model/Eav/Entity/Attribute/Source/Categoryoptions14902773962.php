<?php
class TCustom_Toanlm_Model_Eav_Entity_Attribute_Source_Categoryoptions14902773962 extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    /**
     * Retrieve all options array
     *
     * @return array
     */
    public function getAllOptions()
    {
        $urlPath = $_SERVER['REQUEST_URI'];
        if (strpos($urlPath, 'catalog_category/save/key') !== false) {
            $url = explode("/?isAjax=true", $urlPath);
            $path = parse_url($url[0], PHP_URL_PATH);
            $pathFragments = explode('/', $path);
            $category_id = end($pathFragments);
        } elseif(strpos($urlPath, 'catalog_category/edit/id') !== false) {
            $url = explode("catalog_category/edit/id/", $urlPath);
            $path = parse_url($url[1], PHP_URL_PATH);
            $pathFragments = explode('/', $path);
            $category_id = $pathFragments[0];
        }else {
            $url = explode("/?isAjax=true", $urlPath);
            $path = parse_url($url[0], PHP_URL_PATH);
            $pathFragments = explode('/', $path);
            $category_id = end($pathFragments);
        }    
        
        
        $collectionProduct = Mage::getModel('catalog/category')->load($category_id)
         ->getProductCollection()
         ->addAttributeToSelect('entity_id') // add all attributes - optional
         ->addAttributeToSelect('sku')
         ->addAttributeToFilter('status', 1) // enabled
         ->addAttributeToFilter('visibility', 4) //visibility in catalog,search
         ->setOrder('price', 'ASC') //sets the order by price
         ->getData();

        $products=array();       
        foreach ($collectionProduct as $product) {
            $products[] = array(
                'label' => $product['sku'],
                'value' => (int)$product['entity_id']  
            );
        }

        if (is_null($this->_options)) {
            $this->_options = $products;               
        }
        return $this->_options;
    }

    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $_options = array();
        foreach ($this->getAllOptions() as $option) {
            $_options[$option["value"]] = $option["label"];
        }
        return $_options;
    }

    /**
     * Get a text for option value
     *
     * @param string|integer $value
     * @return string
     */
    public function getOptionText($value)
    {
        $options = $this->getAllOptions();
        foreach ($options as $option) {
            if ($option["value"] == $value) {
                return $option["label"];
            }
        }
        return false;
    }

    /**
     * Retrieve Column(s) for Flat
     *
     * @return array
     */
    public function getFlatColums()
    {
        $columns = array();
        $columns[$this->getAttribute()->getAttributeCode()] = array(
            "type"      => "tinyint(1)",
            "unsigned"  => false,
            "is_null"   => true,
            "default"   => null,
            "extra"     => null
        );

        return $columns;
    }

    /**
     * Retrieve Indexes(s) for Flat
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        $indexes = array();

        $index = "IDX_" . strtoupper($this->getAttribute()->getAttributeCode());
        $indexes[$index] = array(
            "type"      => "index",
            "fields"    => array($this->getAttribute()->getAttributeCode())
        );

        return $indexes;
    }

    /**
     * Retrieve Select For Flat Attribute update
     *
     * @param int $store
     * @return Varien_Db_Select|null
     */
    public function getFlatUpdateSelect($store)
    {
        return Mage::getResourceModel("eav/entity_attribute")
            ->getFlatUpdateSelect($this->getAttribute(), $store);
    }
}

			 