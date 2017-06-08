<?php

class Magestore_Imageoption_Block_Adminhtml_Template_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('templateGrid');
      $this->setDefaultSort('template_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
      $collection = Mage::getModel('imageoption/template')->getCollection();
      $this->setCollection($collection);
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
      $this->addColumn('template_id', array(
          'header'    => Mage::helper('imageoption')->__('ID'),
          'align'     =>'right',
          'width'     => '50px',
          'index'     => 'template_id',
      ));

      $this->addColumn('title', array(
          'header'    => Mage::helper('imageoption')->__('Title'),
          'align'     =>'left',
          'index'     => 'title',
      ));
	  
      $this->addColumn('short_descrip', array(
          'header'    => Mage::helper('imageoption')->__('Description'),
          'align'     =>'left',
          'index'     => 'short_descrip',
      ));	  


      $this->addColumn('status', array(
          'header'    => Mage::helper('imageoption')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
              1 => 'Enabled',
              2 => 'Disabled',
          ),
      ));
	  
        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('imageoption')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('imageoption')->__('Edit'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('imageoption')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('imageoption')->__('XML'));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('template_id');
        $this->getMassactionBlock()->setFormFieldName('template');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('imageoption')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('imageoption')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('imageoption/status')->getOptionArray();

        array_unshift($statuses, array('label'=>'', 'value'=>''));
        $this->getMassactionBlock()->addItem('status', array(
             'label'=> Mage::helper('imageoption')->__('Change status'),
             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
             'additional' => array(
                    'visibility' => array(
                         'name' => 'status',
                         'type' => 'select',
                         'class' => 'required-entry',
                         'label' => Mage::helper('imageoption')->__('Status'),
                         'values' => $statuses
                     )
             )
        ));
        return $this;
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}