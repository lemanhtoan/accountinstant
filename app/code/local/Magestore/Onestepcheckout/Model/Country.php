<?php
/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Magestore
 * @package     Magestore_Geoip
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Geoip Model
 * 
 * @category    Magestore
 * @package     Magestore_Geoip
 * @author      Magestore Developer
 */
class Magestore_Onestepcheckout_Model_Country extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('onestepcheckout/country');
    }
	public function import()
	{
		$data = $this->getData();								
		if($data['country'])
			$data['country'] = trim($data['country'], ' ');
			$data['country'] = trim($data['country'], '"');
		if($data['first_ip'])
			$data['first_ip'] = trim($data['first_ip'], ' ');
			$data['first_ip'] = trim($data['first_ip'], '"');
		if($data['last_ip'])
			$data['last_ip'] = trim($data['last_ip'], ' ');
			$data['last_ip'] = trim($data['last_ip'], '"');
			
		//check exited row
		if(!$data['country']){
			return false;				
		}else{			
			$collection = $this->getCollection()						
							->addFieldToFilter('country',$data['country'])
							->addFieldToFilter('first_ip',$data['first_ip'])
							->addFieldToFilter('last_ip',$data['last_ip'])												
							;        
		}
		
		if(count($collection))
			return false;			
			
		$this->setData($data);
		$this->save();
		
		return $this->getId();
	}
}