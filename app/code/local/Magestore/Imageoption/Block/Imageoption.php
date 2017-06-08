<?php
class Magestore_Imageoption_Block_Imageoption extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getImageoption()     
     { 
        if (!$this->hasData('imageoption')) {
            $this->setData('imageoption', Mage::registry('imageoption'));
        }
        return $this->getData('imageoption');
        
    }
}