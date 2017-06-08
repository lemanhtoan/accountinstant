<?php

class MagentoCenter_Okpay_IpnController extends Mage_Core_Controller_Front_Action
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
            $data = $this->getRequest()->getPost();
            Mage::getModel('okpay/ipn')->processIpnRequest($data);
        } catch (Exception $e) {
            Mage::logException($e);
			header("HTTP/1.1 406 Not Acceptable");
			exit;
        }
    }
}
