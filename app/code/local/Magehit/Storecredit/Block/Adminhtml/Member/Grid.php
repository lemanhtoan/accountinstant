<?php
class Magehit_Storecredit_Block_Adminhtml_Member_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('member_grid');
      $this->setDefaultSort('customer_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
  	  $collection = Mage::getResourceModel('customer/customer_collection')
							            ->addNameToSelect()
							            ->addAttributeToSelect('email');
  	  $resource = Mage::getModel('core/resource');
  	  $customer_table = $resource->getTableName('storecredit/customer');
           
      $collection->getSelect()->joinLeft(
		array('storecredit_customer_entity'=>$customer_table),'e.entity_id = storecredit_customer_entity.customer_id',array('credit_balance'));
	
      $this->setCollection($collection);
      
      return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
  	  $this->addColumn('entity_id', array(
          'header'    => Mage::helper('storecredit')->__('ID'),
          'align'     =>'right',
          'width'     => '100px',
          'index'     => 'entity_id',
      ));
       $this->addColumn('name', array(
            'header'    => Mage::helper('storecredit')->__('Customer Name'),
            'index'     => 'name'
        ));
      $this->addColumn('email', array(
          'header'    => Mage::helper('storecredit')->__('Customer Email'),
          'align'     =>'left',
          'index'     => 'email',
      ));

      $this->addColumn('credit_balance', array(
          'header'    => Mage::helper('storecredit')->__('Credits'),
          'align'     => 'right',
          'width'     => '80px',
          'index'     => 'credit_balance',
          'type'      => 'number',
          'renderer'  => 'storecredit/adminhtml_renderer_credit',
          'filter_condition_callback' => array($this, '_filterCreditCondition'),
      ));
      
       $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('storecredit')->__('Action'),
                'width'     => '80px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('storecredit')->__('Detail'),
                        'url'       => array('base'=> '*/*/edit'),
                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));
		
		$this->addExportType('*/*/exportCsv', Mage::helper('storecredit')->__('CSV'));
		$this->addExportType('*/*/exportXml', Mage::helper('storecredit')->__('XML'));
	  
      	return parent::_prepareColumns();
    }
    
	protected function _filterCreditCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }

       if(isset($value['from']) && $value['from'] != '' && $value['from'] != 0) $this->getCollection()->getSelect()->where("storecredit_customer_entity.credit_balance >= ? ",$value['from']);
	   if(isset($value['to']) && $value['to']!= '' )$this->getCollection()->getSelect()->where("storecredit_customer_entity.credit_balance <= ?",$value['to']);
      
    }

  public function getRowUrl($row)
  {
      return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}