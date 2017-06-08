<?php

class Magestore_Imageoption_Block_Adminhtml_Template_Edit_Tab_Related extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('related_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);

    }
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            else {
                if($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
                }
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    protected function _prepareCollection()
    {
        $data = $this->getRequest()->getPost();
		
		$collection = Mage::getModel('catalog/product')
						->getCollection()
						->addAttributeToSelect('*');					

			//not search
		if(! Mage::getSingleton('core/session')->getData('is_search'))
		{
			$productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }        
			$collection->addFieldToFilter('entity_id', array('in'=>$productIds));
			
			 Mage::getSingleton('core/session')->setData('is_search',true);
		}
	
		$collection->addFieldToFilter('entity_id', array('neq'=>0));
			
        $this->setCollection($collection);
		
		Mage::getSingleton('core/session')->setData('is_search',1);		
		
        return parent::_prepareCollection();
    }


    public function isReadonly()
    {
		return false;
	}

    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn('in_products', array(
                'header_css_class' => 'a-center',
                'type'      => 'checkbox',
                'name'      => 'in_products',
                'values'    => $this->_getSelectedProducts(),
                'align'     => 'center',
                'index'     => 'entity_id'
            ));
        }

        $this->addColumn('entity_id', array(
            'header'    => Mage::helper('catalog')->__('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => Mage::helper('catalog')->__('Name'),
            'index'     => 'name'
        ));

        $this->addColumn('type',
            array(
                'header'=> Mage::helper('catalog')->__('Type'),
                'width' => '100px',
                'index' => 'type_id',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_type')->getOptionArray(),
        ));

        $sets = Mage::getResourceModel('eav/entity_attribute_set_collection')
            ->setEntityTypeFilter(Mage::getModel('catalog/product')->getResource()->getTypeId())
            ->load()
            ->toOptionHash();

        $this->addColumn('set_name',
            array(
                'header'=> Mage::helper('catalog')->__('Attrib. Set Name'),
                'width' => '130px',
                'index' => 'attribute_set_id',
                'type'  => 'options',
                'options' => $sets,
        ));

        $this->addColumn('status',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '90px',
                'index' => 'status',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_status')->getOptionArray(),
        ));

        $this->addColumn('visibility',
            array(
                'header'=> Mage::helper('catalog')->__('Visibility'),
                'width' => '90px',
                'index' => 'visibility',
                'type'  => 'options',
                'options' => Mage::getSingleton('catalog/product_visibility')->getOptionArray(),
        ));

        $this->addColumn('sku', array(
            'header'    => Mage::helper('catalog')->__('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => Mage::helper('catalog')->__('Price'),
            'type'  => 'currency',
            'currency_code' => (string) Mage::getStoreConfig(Mage_Directory_Model_Currency::XML_PATH_CURRENCY_BASE),
            'index'     => 'price'
        ));
		
        $this->addColumn('position', array(
            'header'            => Mage::helper('catalog')->__('Position'),
            'name'              => 'position',
            'type'              => 'number',
            'validate_class'    => 'validate-number',
            'index'             => 'position',
            'editable'          => true,
            'edit_only'         => true,
        ));				


        return parent::_prepareColumns();
    }
	
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('instance_id' => $row->getId()));
	}

    public function getGridUrl()
    {
        return $this->getUrl('*/*/listproductGrid', array('_current' => true, 'id' => $this->getRequest()->getParam('id'), 'store'=> $this->getRequest()->getParam('store')));
    }

/*
    protected function _getSelectedProducts()
    {
        $productIds = $this->getRequest()->getPost('productid', null);
        if (!is_array($productIds)) {
			$template = Mage::getModel('imageoption/template')
							->setId($this->getRequest()->getParam('id'));
            $productIds = $template->getProductIds();
        }

        return $productIds;
    }
*/	
    protected function _getSelectedProducts()
    {
        $products = $this->getProductsRelated();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedRelatedProducts());
        }
        return $products;
    }	
	
    public function getSelectedRelatedProducts()
    {
		$rs = array();
		
		$template = Mage::getModel('imageoption/template')
						->setId($this->getRequest()->getParam('id'));
		$productIds = $template->getProductIds();
			
		if(count($productIds))
		foreach($productIds as $productId)
		{
			$rs[$productId] = array('position'=>0);
		}
			
		return $rs;
    }		
}