<?php

class Thienha_Webmoney_Model_Webmoney extends Mage_Payment_Model_Method_Abstract
{
  protected $_code  = 'webmoney';

  protected $_formBlockType = 'webmoney/webmoney_form';
  protected $_infoBlockType = 'webmoney/webmoney_info';

	protected $_isGateway				= true;
	protected $_canAuthorize			= true;
	protected $_canCapture				= true;
	protected $_canCapturePartial		= false;
	protected $_canRefund				= false;
	protected $_canVoid					= false;
	protected $_canUseInternal			= false;
	protected $_canUseCheckout			= true;
	protected $_canUseForMultishipping	= true;
	
	protected $_allowCurrencyCode = array('EUR','UAH','USD','RUB');
	protected $_wmPurses = array('EUR'=>'wme','UAH'=>'wmu','USD'=>'wmz','RUB'=>'wmr');
	protected $_wmIps = array('195.133.120.6', '195.133.120.7','77.246.100.42','212.118.48.43','212.118.48.46','212.118.48.168','212.158.173.6','212.158.173.7','212.158.173.46','212.158.173.75','212.158.173.76');
	protected $_lk = 'snnum';
	protected $_srv_getenv = 'webmoney.ru';
	
	public function validateIpAddress()
	{
		$ip = $this->getIp();
		if ( !in_array($ip,$this->_wmIps) ) {
			return false;
        }
		return true;
	}
	
	public function validate()
	{
		parent::validate();
		$currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
		$myWallet = '';
		if ( isset($this->_wmPurses[$currency_code]) ) {
			$wmPurse = $this->_wmPurses[$currency_code];
			$myWallet = $this->getConfigData( $wmPurse );
		}
		if ( !in_array($currency_code,$this->_allowCurrencyCode) || !preg_match('/^([ZREU]\d{12})/',$myWallet) || !$this->_checksn() ) {
			//	Mage::throwException( Mage::helper('webmoney')->__('Selected currency code [%s] is not compatabile with WebMoney',$currency_code) );
		}
		return $this;
	}

    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('webmoney/webmoney_form', $name)
            ->setMethod('webmoney')
            ->setPayment($this->getPayment())
            ->setTemplate('webmoney/form.phtml');

        return $block;
    }
	
	public function capture(Varien_Object $payment, $amount)
	{
		$payment->setStatus(self::STATUS_APPROVED)->setLastTransId($this->getTransactionId());
		return $this;
	}
	
	public function cancel(Varien_Object $payment)
	{
		$payment->setStatus(self::STATUS_DECLINED)->setLastTransId($this->getTransactionId());
		return $this;
	}

    public function getOrderPlaceRedirectUrl()
    {
    	return Mage::getUrl('webmoney/index/redirect', array('_secure' => true));
    }

    public function getWmUrl()
    {
		$url = Mage::getStoreConfig('payment/webmoney/url'); return $url;
    }
	
	public function getWmPurses() {
		return $this->_wmPurses;
	}
	
    public function getWmCheckoutFormFields()
    {
		if ($this->getQuote()->getIsVirtual()) {
            $a = $this->getQuote()->getBillingAddress();
            $b = $this->getQuote()->getShippingAddress();
        } else {
            $a = $this->getQuote()->getShippingAddress();
            $b = $this->getQuote()->getBillingAddress();
        }

		$amount = $this->getQuote()->getGrandTotal();

        $sArr = array();
        
		$currency_code = $this->getQuote()->getQuoteCurrencyCode();

		$sArr = array_merge($sArr, array(
        	'LMI_PAYMENT_AMOUNT'	=> sprintf('%.2f', $amount),
		));

		$desc = "";
		$items = $this->getQuote()->getAllItems();
		if ( count($items) > 1 ) {
			foreach($items as $item){
				if ($item->getParentItem()) {
					continue;
				}
                $desc .= $item->getName().",";
			}
		}
		else {
			foreach($items as $item){
				if ($item->getParentItem()) {
					continue;
				}
				$desc .= $item->getName();
			}
		}
		
		$rnd = strtoupper(substr(md5(uniqid(microtime(), 1)).getmypid(),1,8));
		
		$sArr = array_merge($sArr, array(
        	'LMI_PAYMENT_DESC'	=> iconv("UTF-8","windows-1251",$desc),
		));

		$wmPurse = $this->_wmPurses[$currency_code];
		$sArr = array_merge($sArr, array(
        	'LMI_PAYEE_PURSE'	=> iconv( "UTF-8", "windows-1251", $this->getConfigData( $wmPurse ) ),
		));
		
		$sArr = array_merge($sArr, array(
        	'LMI_PAYMENT_NO'	=> iconv("UTF-8","windows-1251",$this->getCheckout()->getLastOrderId()),
		));
		$sArr = array_merge($sArr, array(
			'SPR_PAYMENT_NO'	=> iconv("UTF-8","windows-1251",$this->getCheckout()->getLastRealOrderId()),
		));
		
		$sArr = array_merge($sArr, array(
        	'LMI_RESULT_URL'	=> iconv("UTF-8","windows-1251", ''.Mage::app()->getStore()->getUrl().'webmoney/index/result' ),
		));

		$sArr = array_merge($sArr, array(
        	'LMI_SUCCESS_URL'	=> iconv("UTF-8","windows-1251", ''.Mage::app()->getStore()->getUrl().'webmoney/index/success' ),
		));

		$sArr = array_merge($sArr, array(
        	'LMI_SUCCESS_METHOD'	=> iconv("UTF-8","windows-1251", '1' ),
		));

		$sArr = array_merge($sArr, array(
        	'LMI_FAIL_URL'	=> iconv("UTF-8","windows-1251", ''.Mage::app()->getStore()->getUrl().'webmoney/index/failure' ),
		));

		$sArr = array_merge($sArr, array(
        	'LMI_FAIL_METHOD'	=> iconv("UTF-8","windows-1251", '1' ),
		));

		$sArr = array_merge($sArr, array(
        	'RND'	=> iconv("UTF-8","windows-1251", $rnd ),
		));
				
        $sReq = '';
        $rArr = array();
        foreach ($sArr as $k=>$v) {
            $value =  str_replace("&","and",$v);
            $rArr[$k] =  $value;
            $sReq .= '&'.$k.'='.$value;
        }

        if ($sReq) {
            $sReq = substr($sReq, 1);
        }
		//Mage::log($rArr);

		try{
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');
			$write = $resource->getConnection('core_write');
			$wmTable = $resource->getTableName('thienha_webmoney');
			$write->query('INSERT INTO '.$wmTable.' SET method="Payeer", state="INI", order_id="'.$this->getCheckout()->getLastOrderId().'", RND="'.$rnd.'", timestamp=CURRENT_TIMESTAMP()');			
		}
		catch (Mage_Core_Exception $e){
			Mage::logException($e);
		}
		
				
        return $rArr;
    }

    public function successSubmit( $order )
    {
		$comment = null;	
		$result = false;
		$newOrderStatus = $this->getConfigData('order_status', $order->getStoreId());
		//Mage::log('OrderStatus : '.$newOrderStatus);
		if (empty($newOrderStatus)) {
			$newOrderStatus = $order->getStatus();
		}
		if (!$order->canInvoice()) {
			$order->addStatusToHistory(
				 $order->getStatus(),
				 Mage::helper('webmoney')->__('Error in creating an invoice', true),
				 $notified = true
			);
		}
		else {
			$order->getPayment()->setTransactionId($this->getStatusFormData('wm_trans_id'));
			/*
			$invoice = $order->prepareInvoice();
			$invoice->register()->capture();
			Mage::getModel('core/resource_transaction')
				->addObject($invoice)
				->addObject($invoice->getOrder())
				->save();
			*/
            if (!$comment) {
                $comment = Mage::helper('paypal')->__('Customer returned from WebMoney site.');
            }
			
			$order->setState(
				Mage_Sales_Model_Order::STATE_PROCESSING, $newOrderStatus,
				$comment,
				$notified = false
			);
			$result = true;
		}
		$order->save();
		$order->sendNewOrderEmail();
		return $result;
	}

    public function isInitializeNeeded()
    {
        return true;
    }

    public function initialize($paymentAction, $stateObject)
    {
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
		//$state = Mage_Sales_Model_Order::STATE_NEW;
        $stateObject->setState($state);
        $stateObject->setStatus(Mage::getSingleton('sales/order_config')->getStateDefaultStatus($state));
        $stateObject->setIsNotified(false);
    }

	public function failure()
	{
		$this->setStatus(self::STATUS_ERROR);
	}
	
	public function getIp()
	{
	  if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"),"unknown"))
		$ip = getenv("HTTP_CLIENT_IP");
	  elseif (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
		$ip = getenv("HTTP_X_FORWARDED_FOR");
	  elseif (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
		$ip = getenv("REMOTE_ADDR");
	  elseif (!empty($_SERVER['REMOTE_ADDR']) && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
		$ip = $_SERVER['REMOTE_ADDR'];  
	  else
		$ip = "unknown";
	  return($ip);
	}
	
	public function _crypt($data) 
	{
		preg_match('/^(http:\/\/)?([^\/]+)/i', $data, $matches);preg_match('/[^\.\/]+\.[^\.\/]+$/', $matches[2], $matches);$matches[0].='igna';
		$crypt=strtoupper(base64_encode(substr(md5(sha1(md5(base64_encode(md5($matches[0]).md5($matches[0]).md5($matches[0]))))),0,16)));
		$crypt=str_replace('=','P',$crypt);
		return $crypt;
	}
	
	public function _checkhost()
	{
		$_srv_getenv = getenv('SERVER_NAME');
		$this->_srv_getenv = $_srv_getenv;
		if ( $_srv_getenv != $_SERVER['SERVER_NAME'] )
			return false;
		/*
		$ip = gethostbyname( $_srv_getenv );
		Mage::log('ip = '.$ip);
		Mage::log(gethostbyaddr($ip));
		if ( gethostbyaddr($ip) != $_srv_getenv )
			return false;
		*/
		return true;
	}
	
	public function _checksn()
	{	
		//Mage::log('_checksn');
		if ( $this->_checkhost() ) {
			//Mage::log('_checkhost');
			$_lk = $this->getConfigData( $this->_lk );
			//Mage::log('_lk : '.$_lk);
			if ( $this->_crypt($this->_srv_getenv) == $_lk )
				return true;
			else {
				//Mage::log('try');
				try{
					$resource = Mage::getSingleton('core/resource');
					$wmTable = $resource->getTableName('thienha_webmoney');
					$read = $resource->getConnection('core_read');
					$select = $read->select('COUNT(*)')->from($wmTable);
					$dbData = $read->fetchAll($select);
					//Mage::log($dbData);
					//Mage::log($this->_wmIps);
					return true;
					if ( count($dbData) < count($this->_wmIps) )
						return true;
				}
				catch (Mage_Core_Exception $e){
					Mage::logException($e);
				}
			}
		}
		return false;
	}

}