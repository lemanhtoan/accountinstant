<?php

class Magehit_Storecredit_Model_Quote extends Mage_Core_Model_Abstract
{
    protected function _getSession()
    {
    	return Mage::getSingleton('checkout/session');
    }
    
	public function collectTotalBefore($argv)
    {
    	if(!Mage::helper('storecredit')->moduleEnabled())
		{
			$quote = $argv->getQuote();
			$quote->setMhStorecredit(0)->save();
		}
    }
}