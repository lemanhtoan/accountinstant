<?php

class Magestore_Imageoption_Model_Mysql4_Productoption extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option
{

	public function duplicate(Mage_Catalog_Model_Product_Option $object, $oldProductId, $newProductId)
    {
        $write  = $this->_getWriteAdapter();
        $read   = $this->_getReadAdapter();

        $optionsCond = array();
        $optionsData = array();

        // read and prepare original product options
        $select = $read->select()
            ->from($this->getTable('catalog/product_option'))
            ->where('option_id=?', $object->getId());
        $query = $read->query($select);
        while ($row = $query->fetch()) {
            $optionsData[$row['option_id']] = $row;
            $optionsData[$row['option_id']]['product_id'] = $newProductId;
            unset($optionsData[$row['option_id']]['option_id']);
        }

        // insert options to duplicated product
        foreach ($optionsData as $oId => $data) {
            $write->insert($this->getMainTable(), $data);
            $optionsCond[$oId] = $write->lastInsertId();
        }

        $valueCond = array();
		// copy options prefs
        foreach ($optionsCond as $oldOptionId => $newOptionId) {
            // title
            $table = $this->getTable('catalog/product_option_title');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `title`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $this->_getWriteAdapter()->query($sql);

            // price
            $table = $this->getTable('catalog/product_option_price');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newOptionId . ', `store_id`, `price`, `price_type`'
                . 'FROM `' . $table . '` WHERE `option_id`=' . $oldOptionId;
            $this->_getWriteAdapter()->query($sql);

            $valueCond = Mage::getModel('imageoption/optionvalue')->duplicate($oldOptionId, $newOptionId);

        }
		
        $mapInfo['option'] = $optionsCond;
        $mapInfo['option_type'] = $valueCond;
		
		return $mapInfo;
    }

}