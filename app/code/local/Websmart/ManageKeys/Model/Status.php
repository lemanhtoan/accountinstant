<?php
class Websmart_ManageKeys_Model_Status extends Varien_Object
{
    const STATUS_ENABLED    = 1;
	const STATUS_USED    = 2;
	const STATUS_DISABLED    = 3;
    
    /**
     * get model option as array
     *
     * @return array
     */
    static public function getOptionArray()
    {
        return array(
            self::STATUS_ENABLED    => Mage::helper('managekeys')->__('Enabled'),
			self::STATUS_USED    => Mage::helper('managekeys')->__('Used'),
            self::STATUS_DISABLED   => Mage::helper('managekeys')->__('Disabled')
        );
    }
    
    /**
     * get model option hash as array
     *
     * @return array
     */
    static public function getOptionHash()
    {
        $options = array();
        foreach (self::getOptionArray() as $value => $label) {
            $options[] = array(
                'value'    => $value,
                'label'    => $label
            );
        }
        return $options;
    }
}