<?php
class Websmart_ManageKeys_Block_Adminhtml_Managekeys_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('managekeysGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    
    /**
     * prepare collection for block to display
     *
     * @return Websmart_ManageKeys_Block_Adminhtml_Managekeys_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('managekeys/managekeys')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * prepare columns for this grid
     *
     * @return Websmart_ManageKeys_Block_Adminhtml_Managekeys_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('managekeys')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'id',
        ));

        $this->addColumn('key', array(
            'header'    => Mage::helper('managekeys')->__('Key'),
            'align'     =>'left',
            'index'     => 'key',
        ));

        $this->addColumn('product_id', array(
            'header'    => Mage::helper('managekeys')->__('Product'),
            'width'     => '150px',
            'index'     => 'product_id',
			'renderer'  => 'managekeys/adminhtml_managekeys_renderer_product',
        ));
		
		$this->addColumn('price', array(
            'header'    => Mage::helper('managekeys')->__('Price'),
            'width'     => '150px',
            'index'     => 'price',
        ));
		
		$this->addColumn('price_reseller', array(
            'header'    => Mage::helper('managekeys')->__('Price Reseller'),
            'width'     => '150px',
            'index'     => 'price_reseller',
        ));
		
		$this->addColumn('order_increment_id', array(
            'header'    => Mage::helper('managekeys')->__('Order Increment'),
            'width'     => '150px',
            'index'     => 'order_increment_id',
			'renderer'  => 'managekeys/adminhtml_managekeys_renderer_order',
        ));
		
		$this->addColumn('email', array(
            'header'    => Mage::helper('managekeys')->__('Email Customer purchase'),
            'width'     => '150px',
            'index'     => 'email',
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('managekeys')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'status',
            'type'        => 'options',
            'options'     => Mage::getSingleton('managekeys/status')->getOptionArray(),
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('managekeys')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('managekeys')->__('XML'));

        return parent::_prepareColumns();
    }
    
    /**
     * prepare mass action for this grid
     *
     * @return Websmart_ManageKeys_Block_Adminhtml_Managekeys_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('managekeys_id');
        $this->getMassactionBlock()->setFormFieldName('managekeys');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'        => Mage::helper('managekeys')->__('Delete'),
            'url'        => $this->getUrl('*/*/massDelete'),
            'confirm'    => Mage::helper('managekeys')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('managekeys/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
            'label'=> Mage::helper('managekeys')->__('Change status'),
            'url'    => $this->getUrl('*/*/massStatus', array('_current'=>true)),
            'additional' => array(
                'visibility' => array(
                    'name'    => 'status',
                    'type'    => 'select',
                    'class'    => 'required-entry',
                    'label'    => Mage::helper('managekeys')->__('Status'),
                    'values'=> $statuses
                ))
        ));
        return $this;
    }
}