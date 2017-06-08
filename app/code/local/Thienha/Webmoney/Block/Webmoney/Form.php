<?php

class Thienha_Webmoney_Block_Webmoney_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('webmoney/form.phtml');
    }
}