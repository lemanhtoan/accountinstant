<?php


class Egopay_Egopay_Block_Form extends Mage_Payment_Block_Form
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('egopay/form.phtml');
    }
}