<?php
class Magehit_Storecredit_Block_Adminhtml_Transaction_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

 	public function __construct()
    {
        parent::__construct();
        $this->setId('storecredit_Grid');
        $this->setDefaultSort('history_id');
        $this->setDefaultDir('desc');
	
        $this->setEmptyText(Mage::helper('storecredit')->__('No Transaction Found'));
    }
	protected function _prepareCollection()
  	{
  		$resource = Mage::getModel('core/resource');
  	  	$customer_table = $resource->getTableName('customer/entity');
  	  	
  		$collection = Mage::getResourceModel('storecredit/history_collection');
  		
  		$collection->getSelect()->join(
		array('customer_entity'=>$customer_table),'main_table.customer_id = customer_entity.entity_id',array('email'));
      
      	$this->setCollection($collection);
      	return parent::_prepareCollection();
  	}
  	protected function _prepareColumns()
  	{
  		$this->addColumn('history_id', array(
            'header'    =>  Mage::helper('storecredit')->__('ID'),
            'align'     =>  'left',
            'index'     =>  'history_id',
            'width'     =>  '30px'
        ));
        $this->addColumn('transaction_time', array(
            'header'    =>  Mage::helper('storecredit')->__('Transaction Time'),
            'type'      =>  'datetime',
            'align'     =>  'center',
            'width'		=>  '150px',
            'index'     =>  'transaction_time',
        ));
        
        $this->addColumn('customer_name', array(
          'header'    => Mage::helper('storecredit')->__('Customer Name'),
          'align'     =>'left',
          'index'     => 'customer_id',
          'width'		=>  '150px',
      	  'renderer'  => 'storecredit/adminhtml_renderer_name',
      	  'filter_condition_callback' => array($this, '_filterReferralnameCondition'),
        ));
	    $this->addColumn('email', array(
     		'header'    => Mage::helper('storecredit')->__('Customer Email'),
    	    'align'     =>'left',
	        'width'		=>  '200px',
   	       	'index'     => 'email',
   	   	));
        $this->addColumn('amount', array(
            'header'    =>  Mage::helper('storecredit')->__('Amount'),
            'align'     =>  'left a-right',
            'index'     =>  'amount',
        	'type'      =>  'number',
            'width'		=>  '100px',
        	'renderer'  => 'storecredit/adminhtml_renderer_amount',
        ));

        $this->addColumn('balance', array(
            'header'    =>  Mage::helper('storecredit')->__('Credits'),
            'align'     =>  'left a-right',
            'index'     =>  'balance',
            'width'		=>  '100px',
        	'type'      =>  'number',
            'renderer'  => 'storecredit/adminhtml_renderer_balance',
        ));
        $this->addColumn('transaction_detail', array(
            'header'    =>  Mage::helper('storecredit')->__('Transaction Details'),
            'align'     =>  'left',
            'index'     =>  'transaction_detail',
        	'renderer'  => 'storecredit/adminhtml_renderer_transaction',
        ));
      	 $this->addColumn('status', array(
          	'header'    => Mage::helper('storecredit')->__('Status'),
          	'align'     =>'center',
          	'index'     => 'status',
		  	'type'      => 'options',
          	'options'   => Mage::getSingleton('storecredit/statushistory')->getOptionArray(),
      	    'width'     =>  '120px'
      	));
      	
      	return parent::_prepareColumns();
  	}
	protected function _filterReferralnameCondition($collection, $column)
    {
       if (!$value = $column->getFilter()->getValue()) {
            return;
        }
       $customer_ids = array();
       $value = '%'.$value.'%';
      // $customer_collections =  Mage::getModel('customer/customer')->getCollection()->addFieldToFilter('firstname',array('like' => $value));
       $customer_collections =  Mage::getModel('customer/customer')->getCollection()
       		->addAttributeToFilter(array(
		    array(
		        'attribute' => 'firstname',
		        array('like' => $value),
		        ),
		    array(
		        'attribute' => 'lastname',
		        array('like' => $value),
		        ),
		    ));
       foreach ($customer_collections as $customer_collection) {
       		$customer_ids[] = $customer_collection->getId();
       }
       $this->getCollection()->getSelect()->where("main_table.customer_id in (?)",$customer_ids);
    }

}
