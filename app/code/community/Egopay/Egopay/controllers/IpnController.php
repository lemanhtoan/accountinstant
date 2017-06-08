<?php

class Egopay_Egopay_IpnController extends Mage_Core_Controller_Front_Action
{
    /**
     * Instantiate IPN model and pass IPN request to it
     */
    public function indexAction()
    {
        if (!$this->getRequest()->isPost()) {
           return;
        }
        try {
            Mage::getModel('egopay/ipn')->processIpnRequest();
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }
}
