<?php
class Websmart_ManageKeys_Block_Adminhtml_Managekeys_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('managekeys_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('managekeys')->__('Key Information'));
    }
    
    /**
     * prepare before render block to html
     *
     * @return Websmart_ManageKeys_Block_Adminhtml_Managekeys_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
		$key = $this->getKey();
		if( ! $key || ($key && ($key->getStatus()!=3 )))
		{  
	  	$this->addTab('form_listproduct', array(
          'label'     => Mage::helper('managekeys')->__('Select Product'),
          'title'     => Mage::helper('managekeys')->__('Select Product'),
		  'class'     => 'ajax',
		  'url'   => $this->getUrl('*/*/listproduct',array('_current'=>true,'id'=>$this->getRequest()->getParam('id'))),
	  	));	
		}
        $this->addTab('form_section', array(
            'label'     => Mage::helper('managekeys')->__('Key Information'),
            'title'     => Mage::helper('managekeys')->__('Key Information'),
            'content'   => $this->getLayout()
                                ->createBlock('managekeys/adminhtml_managekeys_edit_tab_form')
                                ->toHtml(),
        ));
        return parent::_beforeToHtml();
    }
	public function getKey()     
    { 
        if (!$this->hasData('managekeys_data')) {
                $this->setData('managekeys_data', Mage::registry('managekeys_data'));
        }
        return $this->getData('managekeys_data');   
    }
}