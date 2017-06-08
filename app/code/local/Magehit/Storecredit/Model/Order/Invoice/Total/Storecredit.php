<?php
class Magehit_Storecredit_Model_Order_Invoice_Total_Storecredit extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
		$order = $invoice->getOrder();
		
        $totalDiscountAmount     = $order->getMhStorecreditDiscountShow();
        $baseTotalDiscountAmount = $order->getMhStorecreditDiscount();
        
        $invoice->setGrandTotal($invoice->getGrandTotal() - $totalDiscountAmount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() - $baseTotalDiscountAmount);
        
        $invoice->setMhStorecredit($order->getMhStorecredit());
        $invoice->setMhStorecreditBuyCredit($order->getMhStorecreditBuyCredit());
        $invoice->setMhStorecreditDiscount($baseTotalDiscountAmount);
        $invoice->setMhStorecreditDiscountShow($totalDiscountAmount);
        
        return $this;
    }


}
