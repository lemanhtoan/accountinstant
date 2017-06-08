<?php

class Websmart_ManageKeys_Block_Adminhtml_Managekeys_Serializer 
		extends Mage_Core_Block_Template
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('websmart/managekeys/serializer.phtml');
		return $this;
	}
	
	public function initSerializerBlock($gridName,$hiddenInputName)
	{
		$grid = $this->getLayout()->getBlock($gridName);
        $this->setGridBlock($grid)
                 ->setInputElementName($hiddenInputName);
	}
}