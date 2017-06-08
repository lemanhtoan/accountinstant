<?php

class Magehit_Storecredit_Model_Action extends Varien_Object
{
    const ADDITION				= 1;
    const SUBTRACTION			= -1;
	

    static public function getOptionArray()
    {
        return array(
        	self::SUBTRACTION  			 	=> Mage::helper('storecredit')->__('Subtraction'),
            self::ADDITION    				=> Mage::helper('storecredit')->__('Addition'),
        );
    }
}