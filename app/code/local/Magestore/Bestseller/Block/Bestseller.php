<?php
class Magestore_Bestseller_Block_Bestseller extends Mage_Catalog_Block_Product_List
{
	public function __construct()
    {
        parent::__construct();
    }
	protected function _getProductCollection()
    {
		if (is_null($this->_productCollection)) {
		$storeId    = Mage::app()->getStore()->getId();

        $this->_productCollection = Mage::getResourceModel('reports/product_collection')
            ->addOrderedQty()
            ->addAttributeToSelect('*')
            ->addStoreFilter($storeId)
            ->addOrderedQty()
            ->setOrder('ordered_qty', 'desc'); 
        
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_productCollection);
		}
		$this->_productCollection->setPageSize(3)->setCurPage(1);
		return $this->_productCollection;
	}
}
?>