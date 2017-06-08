<?php

class Magestore_Imageoption_Block_Adminhtml_Template_Edit_Tab_Options extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options
{

    protected function _prepareLayout()
    {
		parent::_prepareLayout();
    
        $this->setChild('options_box',
            $this->getLayout()->createBlock('imageoption/adminhtml_template_edit_tab_options_option')
		);
		
	}
}