<?php
class Magehit_Storecredit_Model_Order_Creditmemo_Total_Storecredit extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo)
    {

        $order = $creditmemo->getOrder();

        $totalDiscountAmount     = $order->getMhStorecreditDiscountShow();
        $baseTotalDiscountAmount = $order->getMhStorecreditDiscount();
        
        $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $totalDiscountAmount);
        $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseTotalDiscountAmount);
        
        $creditmemo->setMhStorecredit($order->getMhStorecredit());
        $creditmemo->setMhStorecreditBuyCredit($order->getMhStorecreditBuyCredit());
        $creditmemo->setMhStorecreditDiscount($baseTotalDiscountAmount);
        $creditmemo->setMhStorecreditDiscountShow($totalDiscountAmount);

        
        return $this;
    }
}
