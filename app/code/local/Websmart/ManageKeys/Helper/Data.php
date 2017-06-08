<?php
class Websmart_ManageKeys_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getActionConfig($code, $store = null){
		return Mage::getStoreConfig('managekeys/general/'.$code,$store);
	}
	public function visitor_country()
	{
		$client  = @$_SERVER['HTTP_CLIENT_IP'];
		$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
		$remote  = $_SERVER['REMOTE_ADDR'];
		$result  = "Unknown";
		if(filter_var($client, FILTER_VALIDATE_IP))
		{
			$ip = $client;
		}
		elseif(filter_var($forward, FILTER_VALIDATE_IP))
		{
			$ip = $forward;
		}
		else
		{
			$ip = $remote;
		}

		$ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));

		if($ip_data && $ip_data->geoplugin_countryCode != null)
		{
			$result = $ip_data->geoplugin_countryCode;
		}

		return $result;
	}
	public function isCountryLimit(){
		$country=$this->visitor_country();
		$allcountrylimit=$this->getActionConfig('detect_country',Mage::app()->getStore()->getId());
		if(strpos($allcountrylimit, $country)===false){
			return false;
		}else{
			return true;
		}
	}
	public function isRobots() {
		if (!$this->getActionConfig('detect_software',Mage::app()->getStore()->getId()))
            return false;
        if (empty($_SERVER['HTTP_USER_AGENT']))
            return true;
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'])
            return true;
        define("UNKNOWN", 0);
        define("TRIDENT", 1);
        define("GECKO", 2);
        define("PRESTO", 3);
        define("WEBKIT", 4);
        define("VALIDATOR", 5);
        define("ROBOTS", 6);

        if (!isset($_SESSION["info"]['browser'])) {

            $_SESSION["info"]['browser']['engine'] = UNKNOWN;
            $_SESSION["info"]['browser']['version'] = UNKNOWN;
            $_SESSION["info"]['browser']['platform'] = 'Unknown';

            $navigator_user_agent = ' ' . strtolower($_SERVER['HTTP_USER_AGENT']);

            if (strpos($navigator_user_agent, 'linux')) :
                $_SESSION["info"]['browser']['platform'] = 'Linux';
            elseif (strpos($navigator_user_agent, 'mac')) :
                $_SESSION["info"]['browser']['platform'] = 'Mac';
            elseif (strpos($navigator_user_agent, 'win')) :
                $_SESSION["info"]['browser']['platform'] = 'Windows';
            endif;

            if (strpos($navigator_user_agent, "trident")) {
                $_SESSION["info"]['browser']['engine'] = TRIDENT;
                $_SESSION["info"]['browser']['version'] = floatval(substr($navigator_user_agent, strpos($navigator_user_agent, "trident/") + 8, 3));
            } elseif (strpos($navigator_user_agent, "webkit")) {
                $_SESSION["info"]['browser']['engine'] = WEBKIT;
                $_SESSION["info"]['browser']['version'] = floatval(substr($navigator_user_agent, strpos($navigator_user_agent, "webkit/") + 7, 8));
            } elseif (strpos($navigator_user_agent, "presto")) {
                $_SESSION["info"]['browser']['engine'] = PRESTO;
                $_SESSION["info"]['browser']['version'] = floatval(substr($navigator_user_agent, strpos($navigator_user_agent, "presto/") + 6, 7));
            } elseif (strpos($navigator_user_agent, "gecko")) {
                $_SESSION["info"]['browser']['engine'] = GECKO;
                $_SESSION["info"]['browser']['version'] = floatval(substr($navigator_user_agent, strpos($navigator_user_agent, "gecko/") + 6, 9));
            } elseif (strpos($navigator_user_agent, "robot"))
                $_SESSION["info"]['browser']['engine'] = ROBOTS;
            elseif (strpos($navigator_user_agent, "spider"))
                $_SESSION["info"]['browser']['engine'] = ROBOTS;
            elseif (strpos($navigator_user_agent, "bot"))
                $_SESSION["info"]['browser']['engine'] = ROBOTS;
            elseif (strpos($navigator_user_agent, "crawl"))
                $_SESSION["info"]['browser']['engine'] = ROBOTS;
            elseif (strpos($navigator_user_agent, "search"))
                $_SESSION["info"]['browser']['engine'] = ROBOTS;
            elseif (strpos($navigator_user_agent, "w3c_validator"))
                $_SESSION["info"]['browser']['engine'] = VALIDATOR;
            elseif (strpos($navigator_user_agent, "jigsaw"))
                $_SESSION["info"]['browser']['engine'] = VALIDATOR;
            else {
                $_SESSION["info"]['browser']['engine'] = ROBOTS;
            }
            if ($_SESSION["info"]['browser']['engine'] == ROBOTS)
                return true;
        }
        return false;
    }

    public function isProxys() {
        $useheader = $this->getActionConfig('detect_proxy',Mage::app()->getStore()->getId());
        $usehostbyaddr = $this->getActionConfig('detect_proxy_hostbyaddr',Mage::app()->getStore()->getId());
        $usebankip = $this->getActionConfig('detect_proxy_bankip',Mage::app()->getStore()->getId());
        if ($useheader) {
			if($this->visitor_country()=='Anonymous Proxy')
				return TRUE;
            $header = $this->getActionConfig('detect_proxy_header',Mage::app()->getStore()->getId());
            $arrindex = explode(',', $header);
            $headerarr = Mage::getModel('managekeys/system_config_source_headerdetectproxy')->getOptionList();
            foreach ($arrindex as $index) {
                if (isset($_SERVER[$headerarr[$index]])) {
                    return TRUE;
                }
            }
        }
        if ($usebankip) {
            $arrbankip = explode(';', $usebankip);
            $ip = $_SERVER['REMOTE_ADDR'];
            foreach ($arrbankip as $bankip) {
                if (preg_match('/' . $bankip . '/', $ip, $match))
                    return TRUE;
            }
        }
        if ($usehostbyaddr) {
            $host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            if ($host != $_SERVER['REMOTE_ADDR'])
                return TRUE;
        }
        return FALSE;
    }

    public function addKeysToOrder($invoice){
		if($invoice->getKeyIds()) return true;
		$keys_order=array();
		    foreach ($invoice->getAllItems() as $item) {
				if ($item->getOrderItem()->isDummy() || $item->getKeyIds()) {
					continue;
				}
				$keys=$this->getKeyByProductId($item->getProductId(),$item->getQty());
				if(!$keys) return false;
				$item->setKeyIds(implode(',',Mage::getModel('managekeys/managekeys')->getCollection()->addFieldToFilter('id',array('in'=>$keys))->getAllKeys()));
				$keys_order = array_merge($keys_order,$keys);
			}
		$allkey=Mage::getModel('managekeys/managekeys')->getCollection()->addFieldToFilter('id',array('in'=>$keys_order));
		foreach($allkey as $key){
			$key->setorder_id($invoice->getOrder()->getId())
				->setorder_increment_id($invoice->getOrder()->getIncrementId())
				->setStatus(2)
				->setEmail($invoice->getOrder()->getCustomerEmail())
				->save();
		}
		$invoice->setKeyIds(implode(',',Mage::getModel('managekeys/managekeys')->getCollection()->addFieldToFilter('id',array('in'=>$keys_order))->getAllKeys()));
		if($keys_order) return true;
	}
	public function getKeyByProductId($productid,$qty=1){
			$array= array();
			for($i=1;$i<=$qty;$i++){
				if($key=$this->getKeyByProductIdAPI($productid,$array)){
					$array[]=$key;
				}else{
					return false;
				}
			}
			if($qty != count($array)) return false;
			return $array;
	}
	public function getKeyByProductIdAPI($productid,$array){
		$keys=Mage::getModel('managekeys/managekeys')->getCollection()
			->addFieldToFilter('id',array('nin'=>$array)) 
			->addFieldToFilter('product_id',$productid)
			->addFieldToFilter('status',1)
			;
		if($keys->getSize()){
			return $keys->getFirstItem()->getId();
		}else{
			//return false;
			$product=Mage::getModel('catalog/product')->load($productid);
			$response = file_get_contents($product->getapi_url());
		/* 	Zend_Debug::dump($response);
			die(); */
			$key=Mage::getModel('managekeys/managekeys')
				->setKey('getkeyapi')
				->setProductId($product->getId())
				->setPrice($product->getFinalPrice())
				->setCreatedTime(now())
				->save()
				;
			return $key->getId();
		}
		return false;	
	}
	
}