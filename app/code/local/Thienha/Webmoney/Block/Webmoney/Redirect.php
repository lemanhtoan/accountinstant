<?php

class Thienha_Webmoney_Block_Webmoney_Redirect extends Mage_Core_Block_Abstract
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
        
		$rtext = Mage::getStoreConfig('payment/webmoney/redirect_text');
        $html = '<html><body>';
		if ( strlen( $rtext ) == 0 ) {
			$html.= iconv("UTF-8","windows-1251",Mage::helper('webmoney')->__('You will be redirected to WebMoney in a few seconds.'));
		}
		else {
			$html.= $rtext;
		}
        $html.= $form->toHtml();
        $html.= '<script type="text/javascript">document.getElementById("thienha_webmoney_checkout").submit();</script>';
        $html.= '</body></html>';

        return $html;
    }
}