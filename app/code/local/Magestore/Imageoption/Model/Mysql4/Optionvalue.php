<?php

class Magestore_Imageoption_Model_Mysql4_Optionvalue extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Value
{

    public function duplicate(Mage_Catalog_Model_Product_Option_Value $object, $oldOptionId, $newOptionId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('option_id=?', $oldOptionId);
        $valueData = $this->_getReadAdapter()->fetchAll($select);

        $valueCond = array();

        foreach ($valueData as $data) {
            $optionTypeId = $data[$this->getIdFieldName()];
            unset($data[$this->getIdFieldName()]);
            $data['option_id'] = $newOptionId;

            $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
            $valueCond[$optionTypeId] = $this->_getWriteAdapter()->lastInsertId();
        }

        unset($valueData);

        foreach ($valueCond as $oldTypeId => $newTypeId) {
            // price
            $table = $this->getTable('catalog/product_option_type_price');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newTypeId . ', `store_id`, `price`, `price_type`'
                . 'FROM `' . $table . '` WHERE `option_type_id`=' . $oldTypeId;
            $this->_getWriteAdapter()->query($sql);

            // title
            $table = $this->getTable('catalog/product_option_type_title');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newTypeId . ', `store_id`, `title`'
                . 'FROM `' . $table . '` WHERE `option_type_id`=' . $oldTypeId;
            $this->_getWriteAdapter()->query($sql);
        }

        return $valueCond;
    }
	
	public function duplicateSelf($oldOptionId,$newOptionId,$option_type_id)
	{
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ->where('option_id=?', $oldOptionId)
            ->where('option_type_id=?', $option_type_id);
        $valueData = $this->_getReadAdapter()->fetchAll($select);

        $valueCond = array();

        foreach ($valueData as $data) {
            $optionTypeId = $data[$this->getIdFieldName()];
            unset($data[$this->getIdFieldName()]);
            $data['option_id'] = $newOptionId;

            $this->_getWriteAdapter()->insert($this->getMainTable(), $data);
            $valueCond[$optionTypeId] = $this->_getWriteAdapter()->lastInsertId();
        }

        unset($valueData);

        foreach ($valueCond as $oldTypeId => $newTypeId) {
            // price
            $table = $this->getTable('catalog/product_option_type_price');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newTypeId . ', `store_id`, `price`, `price_type`'
                . 'FROM `' . $table . '` WHERE `option_type_id`=' . $oldTypeId;
            $this->_getWriteAdapter()->query($sql);

            // title
            $table = $this->getTable('catalog/product_option_type_title');
            $sql = 'REPLACE INTO `' . $table . '` '
                . 'SELECT NULL, ' . $newTypeId . ', `store_id`, `title`'
                . 'FROM `' . $table . '` WHERE `option_type_id`=' . $oldTypeId;
            $this->_getWriteAdapter()->query($sql);
        }

        return $newTypeId;		
	}
}