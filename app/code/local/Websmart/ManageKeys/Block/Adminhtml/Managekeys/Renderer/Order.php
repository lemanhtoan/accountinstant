<?php
class Websmart_ManageKeys_Block_Adminhtml_Managekeys_Renderer_Order extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function render(Varien_Object $row){			
		return '<a href="'.$this->getUrl('adminhtml/sales_order/view/', array('order_id' => $row->getOrderId() )).'">' . '#'. $row->getOrderIncrementId() . '</a>';
	}
}