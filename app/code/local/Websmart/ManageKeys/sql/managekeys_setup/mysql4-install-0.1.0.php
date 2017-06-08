<?php
$installer = $this;

$installer->startSetup();

/**
 * create managekeys table
 */
 $entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("catalog_product");
$entity_type_id = $entity_type->getId();
$collection = Mage::getModel("eav/entity_attribute")
			->getCollection()
			->addFieldToFilter("entity_type_id",$entity_type_id)
			->addFieldToFilter("attribute_code","api_url");
			
if(!count($collection))
{
	$attribute = $collection->getFirstItem();
	$data = array();
	$data['id'] = null;
	$data['entity_type_id'] = $entity_type_id;
	$data['attribute_code'] = "api_url";
	$data['backend_type'] = "text";
	$data['frontend_input'] = "text";
	$data['frontend_label'] = 'Api Url';     
	$attribute->setData($data);
	$attribute->save();
	
	$resource = Mage::getSingleton('core/resource');
	$read= $resource->getConnection('core_read');	
	$write = $resource->getConnection('core_write');

	//get entity_type_id
	$entity_type = Mage::getSingleton("eav/entity_type")->loadByCode("catalog_product");
	$entity_type_id = $entity_type->getId();

	//get attribute_set_id	
	$select = $read->select()
				   ->from($this->getTable("eav_attribute_set"),array('attribute_set_id'))
				   ->where("entity_type_id=?",$entity_type_id);
	$attribute_sets = $read->fetchAll($select);		

	foreach($attribute_sets as $attribute_set) {
		$attribute_set_id = $attribute_set['attribute_set_id'];
		$select = $read->select()
					   ->from($this->getTable("eav_attribute"),array('attribute_id'))
					   ->where("entity_type_id=?",$entity_type_id)
					   ->where("attribute_code=?","api_url");
		$attribute = $read->fetchRow($select);		
		$attribute_id = $attribute['attribute_id'];

		$select = $read->select()
					   ->from($this->getTable("eav_attribute_group"),array('attribute_group_id'))
					   ->where("attribute_set_id=?",$attribute_set_id)
					   ->where("attribute_group_name=?","General");
		$attribute_group = $read->fetchRow($select);		
		$attribute_group_id = $attribute_group['attribute_group_id'];

		$write->beginTransaction();
		$write->insert($this->getTable("eav_entity_attribute"),array("entity_type_id"=>$entity_type_id,"attribute_set_id"=>$attribute_set_id,"attribute_group_id"=>$attribute_group_id,"attribute_id"=>$attribute_id,"sort_order"=>0));
		$write->commit();
	}
}
 
$installer->getConnection()->addColumn($this->getTable('sales/invoice'), 'key_ids', 'text NOT NULL default ""');
$installer->getConnection()->addColumn($this->getTable('sales/invoice_item'), 'key_ids', 'text NOT NULL default ""');
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('managekeys')};

CREATE TABLE {$this->getTable('managekeys')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `key` varchar(255) NOT NULL default '',
  `product_id` int(11) NULL,
  `price` decimal(12,4) default '0',
  `price_reseller` decimal(12,4) default '0',
  `order_id` varchar(255) NOT NULL default '',
  `order_increment_id` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup();

