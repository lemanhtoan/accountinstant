<?php
class Websmart_ManageKeys_Block_Adminhtml_Managekeys_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * prepare tab form's information
     *
     * @return Websmart_ManageKeys_Block_Adminhtml_Managekeys_Edit_Tab_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        
        if (Mage::getSingleton('adminhtml/session')->getManageKeysData()) {
            $data = Mage::getSingleton('adminhtml/session')->getManageKeysData();
            Mage::getSingleton('adminhtml/session')->setManageKeysData(null);
        } elseif (Mage::registry('managekeys_data')) {
            $data = Mage::registry('managekeys_data');//->getData();
        }
        $fieldset = $form->addFieldset('managekeys_form', array(
            'legend'=>Mage::helper('managekeys')->__('Item information')
        ));

        $fieldset->addField('key', 'text', array(
            'label'        => Mage::helper('managekeys')->__('Key'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'key',
        ));
			$data['product_name']=Mage::getModel('catalog/product')->load($data->getProductId())->getName();
			$note= '<a target="_blank" href="'. $this->getUrl('adminhtml/catalog_product/edit',array('id'=>$data->getProductId())) .'">'. $this->__('view') .'</a>
			<input type="hidden" name="product_id" id="product_id" value="'. $data->getProductId() .'">';
			$fieldset->addField('product_name', 'text', array(
				'label'     => Mage::helper('managekeys')->__('Product Name'),
				'class'     => 'required-entry',
				'required'  => true,
				'readonly'  => 'readonly',
				'name'      => 'product_name',
				'note'      =>$note,
			));	 
		$fieldset->addField('price', 'text', array(
            'label'        => Mage::helper('managekeys')->__('Price'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'price',
        ));
		
		$fieldset->addField('price_reseller', 'text', array(
            'label'        => Mage::helper('managekeys')->__('Price Reseller'),
            'class'        => 'required-entry',
            'required'    => true,
            'name'        => 'price_reseller',
        ));
		
		$fieldset->addField('item_id', 'text', array(
            'label'        => Mage::helper('managekeys')->__('Item Order'),
            'name'        => 'item_id',
        ));
		
		$fieldset->addField('email', 'text', array(
            'label'        => Mage::helper('managekeys')->__('Email Customer purchase'),
            'name'        => 'email',
        ));
		
        $fieldset->addField('status', 'select', array(
            'label'        => Mage::helper('managekeys')->__('Status'),
            'name'        => 'status',
            'values'    => Mage::getSingleton('managekeys/status')->getOptionHash(),
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}