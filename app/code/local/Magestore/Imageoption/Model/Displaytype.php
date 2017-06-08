<?php

class Magestore_Imageoption_Model_Displaytype 
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('imageoption')->__('Horizontal')),
            array('value'=>0, 'label'=>Mage::helper('imageoption')->__('Vertical')),
        );
    }
}