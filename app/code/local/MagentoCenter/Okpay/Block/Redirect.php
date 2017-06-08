<?php


class MagentoCenter_Okpay_Block_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $okpay = Mage::getModel('okpay/checkout');

        $form = new Varien_Data_Form();
        $form->setAction($okpay->getOkpayUrl())
            ->setId('pay')
            ->setName('pay')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($okpay->getOkpayCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html = '<html><body>';
        $html.= $this->__('Redirect to Okpay.com ...');
        $html.= '<hr>';
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("pay").submit();</script>';
        $html.= '</body></html>';
        

        return $html;
    }
}
