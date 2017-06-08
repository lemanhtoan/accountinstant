<?php

class Magehit_Storecredit_Model_Statushistory extends Varien_Object
{
    const PENDING				= 1;		
    const COMPLETE				= 2;
    const CANCELLED				= 3;
    const CLOSED				= 4;
	

    static public function getOptionArray()
    {
        return array(
            self::PENDING    				=> Mage::helper('storecredit')->__('Pending'),
            self::COMPLETE  			 	=> Mage::helper('storecredit')->__('Complete'),
            self::CANCELLED  			 	=> Mage::helper('storecredit')->__('Cancelled'),
            self::CLOSED  			 	    => Mage::helper('storecredit')->__('Closed'),

        );
    }
    
    static public function getLabel($type)
    {
    	$options = self::getOptionArray();
    	return $options[$type];
    }
}