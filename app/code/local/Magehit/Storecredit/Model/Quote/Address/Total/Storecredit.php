<?php
class Magehit_Storecredit_Model_Quote_Address_Total_Storecredit extends Mage_Sales_Model_Quote_Address_Total_Abstract {	
	
	public function __construct() {
		$this->setCode('mh_storecredit');
	}
	
	public function collect(Mage_Sales_Model_Quote_Address $address) {
		
		parent::collect($address);
		$quote = $address->getQuote();
		
		$this->_setAmount(0);
		$this->_setBaseAmount(0);
		$credit_max_checkout = 0;
		$tax = 0;
		$shipping = 0;
		$mh_credit_buy = 0;
		$credit_min_checkout = 0;
		
		$store_id = $quote->getStore()->getId();
		$customer_id = $quote->getCustomerId();
		$mh_storecredit = $quote->getMhStorecredit();
		
		$items = $address->getAllVisibleItems();
		if (!count($items )) {
			return $this;
		}
		if ($customer_id) {
			
			$tax = $address->getBaseTaxAmount();
			$shipping = $address->getBaseShippingInclTax();
			
			$baseGrandTotal = $address->getBaseGrandTotal() - $tax - $shipping;
			if ($quote->getMhStorecredit() > $baseGrandTotal && $baseGrandTotal > 0) $quote->setMhStorecredit($baseGrandTotal)->save();
		
			$credit_max_checkout = Mage::helper('storecredit')->getMaxCreditToCheckOut($baseGrandTotal,$quote,$customer_id,$store_id);
			$credit_min_checkout = Mage::helper('storecredit')->getMinCreditConfig($baseGrandTotal,$store_id);
			
			if ($quote->getMhStorecredit() > $credit_max_checkout) $quote->setMhStorecredit($credit_max_checkout) ->save();

			if($quote->getMhStorecredit() < $credit_min_checkout) $quote->setMhStorecredit(0)->save();
			
		}else{
			$quote->setMhStorecredit(0)->save();
		}
		
		foreach ($items as $item) {
			
			$product_id = $item->getProductId ();
			$qty = $item->getQty();
			$buy_credit = (int) $qty * Mage::getModel('catalog/product')->load($product_id)->getData('mh_storecredit');
			$mh_credit_buy += $buy_credit;
		}
		
		$mh_storecredit_discount_show = round(Mage::helper('core')->currency($mh_storecredit, false, false ), 2);
		
		$quote->setMhStorecreditCheckoutMax($credit_max_checkout)->save();
		$quote->setMhStorecreditCheckoutMin($credit_min_checkout)->save();
		$quote->setMhStorecreditBuyCredit($mh_credit_buy)->save();
		$quote->setMhStorecreditDiscount($mh_storecredit)->save();
		$quote->setMhStorecreditDiscountShow($mh_storecredit_discount_show)->save();
		
		$address->setMhStorecredit($mh_storecredit);
		$address->setMhStorecreditBuyCredit($mh_credit_buy);
		$address->setMhStorecreditDiscount($mh_storecredit);
		$address->setMhStorecreditDiscountShow($mh_storecredit_discount_show);
		
		$address->setGrandTotal($address->getGrandTotal() - $address->getMhStorecreditDiscountShow() );
		$address->setBaseGrandTotal($address->getBaseGrandTotal() - ($address->getMhStorecreditDiscount()) );
		
		return $this;
	}
	
	public function fetch(Mage_Sales_Model_Quote_Address $address) {
	
		$items = $address->getAllVisibleItems();
		if (! count($items )) {
			return $this;
		}
		
	   $store_id = Mage::app()->getStore()->getId();
       $amount = $address->getMhStorecreditDiscountShow();
       $credit = $address->getMhStorecredit();

        if ($amount!=0) {
        	$title = Mage::helper('storecredit')->__('Discount (Used credits)');
            $address->addTotal(array(
                'code'=> $this->getCode(),
                'title'=>$title,
                'value'=>-$amount,
            	'strong'    => false
            ));
        }
        return $this;
	}

}
