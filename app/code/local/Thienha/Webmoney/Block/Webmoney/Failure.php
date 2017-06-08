<?php
class Thienha_Webmoney_Block_Webmoney_Failure extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $wm = Mage::getModel('webmoney/webmoney');

        $form = new Varien_Data_Form();
        $form->setAction($wm->getWmUrl())
            ->setId('thienha_webmoney_checkout')
            ->setName('thienha_webmoney_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($wm->getWmCheckoutFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }
        $html = '<html><body>';
        $html.= iconv("UTF-8","windows-1251",$this->__('You will be redirected to WebMoney in a few seconds.'));
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("thienha_webmoney_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }

}