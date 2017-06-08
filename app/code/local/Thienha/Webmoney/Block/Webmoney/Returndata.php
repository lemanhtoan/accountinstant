<?php

class Thienha_Webmoney_Block_Webmoney_Returndata extends Mage_Core_Block_Abstract
{
    public function dataToHtml($data) {
        $html = '<html><body>';
        $html.= $data;
        $html.= '<script type="text/javascript">document.getElementById("thienha_webmoney_checkout").submit();</script>';
        $html.= '</body></html>';
        return $html;
    }
}