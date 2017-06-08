<?php
class Thienha_Webmoney_IndexController extends Mage_Core_Controller_Front_Action
{
    protected $_order;

    public function getCheckout()
    {
    	return Mage::getSingleton('checkout/session');
    }

    public function getOrder()
    {
        if ($this->_order == null) {
            $session = $this->getCheckout();
            $this->_order = Mage::getModel('sales/order');
            $this->_order->loadByAttribute('entity_id', $session->getLastOrderId());
        }
        return $this->_order;
    }

    public function loadOrder($id)
    {
		$order = Mage::getModel('sales/order');
		$order->loadByAttribute('entity_id',$id);
        return $order;
    }

    protected function _expireAjax()
    {
        if (!Mage::getSingleton('checkout/session')->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    public function getWm()
    {
        return Mage::getSingleton('webmoney/webmoney');
    }


    public function redirectAction()
    {
		/* if ( !$this->getWm()->_checksn() ){
			$this->loadLayout();$this->renderLayout();$this->_redirect('checkout/cart');
		}
		$session = $this->getCheckout();
		$session->setWmiQuoteId($session->getQuoteId());
		$session->setWmiRealOrderId($session->getLastRealOrderId()); */

		$order = $this->getOrder();
		$session = Mage::getSingleton('checkout/session');
		$order_id = $session->getLastRealOrderId();
		$order->loadByIncrementId($order_id);
		
		/* if (!$order->getId()) {
		  $this->norouteAction(); return;
		}  		
  		$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_HOLDED, Mage::helper('webmoney')->__('Customer was redirected to WebMoney.'));
  		$order->save();

		$this->getResponse()->setHeader('Content-type', 'text/html; charset=windows-1251')
		->setBody($this->getLayout()->createBlock('webmoney/webmoney_redirect')->toHtml()); */
		
		$m_shop = '27379057';
		$m_orderid = $order_id;
		$m_amount = number_format($order->getBaseTotalDue(), 2, '.', '');
		$m_curr = 'USD';
		$m_desc = base64_encode('Buy Premium Key');
		$m_key = 'password2014#';

		$arHash = array(
				$m_shop,
				$m_orderid,
				$m_amount,
				$m_curr,
				$m_desc,
				$m_key
		);
		$sign = strtoupper(hash('sha256', implode(':', $arHash)));
		$response  = '<html><head><title>Redirection</title></head><body>';
		$response .= '<form method="GET" action="//payeer.com/merchant/" id="payeer_payment_form">';
		$response .='<input type="hidden" name="m_shop" value="'.$m_shop.'">';
		$response .='<input type="hidden" name="m_orderid" value="'.$m_orderid.'">';
        $response .='<input type="hidden" name="m_amount" value="'.$m_amount.'">';
        $response .='<input type="hidden" name="m_curr" value="'.$m_curr.'">';
        $response .='<input type="hidden" name="m_desc" value="'.$m_desc.'">';
        $response .='<input type="hidden" name="m_sign" value="'.$sign.'"> ';
		$response .= '</form>';
		$response .= '<script type="text/javascript">document.getElementById("payeer_payment_form").submit();</script></body></html>';
        $this->getResponse()->setBody($response);
    }

    public function returnData($data)
    {
		$this->getResponse()->setHeader('Content-type', 'text/html; charset=windows-1251')
		->setBody($this->getLayout()->createBlock('webmoney/webmoney_returndata')->dataToHtml($data));
    }

    public function successAction()
    {
		if (!$this->getRequest()->isPost()) {
			$this->_redirect(''); return;
		}
		$paymentNo = $this->getRequest()->getParam('LMI_PAYMENT_NO');
		$wmInvsNumber = $this->getRequest()->getParam('LMI_SYS_INVS_NO');
		$wmTransaction = $this->getRequest()->getParam('LMI_SYS_TRANS_NO');
		$wmTransactionDate = $this->getRequest()->getParam('LMI_SYS_TRANS_DATE');
		$rnd = $this->getRequest()->getParam('RND');
		
		try{
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');
			$write = $resource->getConnection('core_write');
			$wmTable = $resource->getTableName('thienha_webmoney');

			$order = $this->loadOrder($paymentNo);
			if ( !$order->getId() ) {
				$select = $read->select();
				$select->from($wmTable,array('transaction_id','state','order_id','rnd','lmi_sys_invs_no','lmi_sys_trans_no','lmi_sys_trans_date'))
					->where('RND=?',$rnd)
					->where('state="SUC"');
				$dbData = $read->fetchRow($select);
				if ( $dbData >= 1 )
					$write->query('UPDATE '.$wmTable.' SET state="FAL", timestamp=CURRENT_TIMESTAMP() WHERE transaction_id="'.$dbData['transaction_id'].'"');
				Mage::throwException( Mage::helper('wmi')->__('Order '.$paymentNo.' not found') );
			}		
			$select = $read->select();
			$select->from($wmTable,array('transaction_id','state','order_id','rnd','lmi_sys_invs_no','lmi_sys_trans_no','lmi_sys_trans_date'))
				->where('order_id=?',$paymentNo)
				->where('RND=?',$rnd)
				->where('state="SUC"');
			$dbData = $read->fetchRow($select);
			
			if ( $dbData >= 1 && $dbData['lmi_sys_invs_no'] == $wmInvsNumber &&
					$dbData['lmi_sys_trans_no'] == $wmTransaction && $dbData['lmi_sys_trans_date'] == $wmTransactionDate &&
					$order->getState() != Mage_Sales_Model_Order::STATE_CANCELED )
			{
				$temp = 'LMI_SYS_INVS_NO = '.$wmInvsNumber.', LMI_SYS_TRANS_NO = '.$wmTransaction.', LMI_SYS_TRANS_DATE = '.$wmTransactionDate;
				$order->addStatusToHistory( $order->getStatus(), $temp );
				if ( $this->getWm()->successSubmit($order) ) {
					$write->query('UPDATE '.$wmTable.' SET state="INV", timestamp=CURRENT_TIMESTAMP() WHERE transaction_id="'.$dbData['transaction_id'].'"');			
				}
				Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();
				$this->_redirect('checkout/onepage/success', array('_secure'=>true));
			}
			else{
				$errorMsg = $errorMsg = Mage::helper('wmi')->__('There was an error occurred during adoption process.');		
				$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $errorMsg, true);
				$order->save();
				Mage::getSingleton('checkout/session')->unsLastRealOrderId();
				Mage::throwException( $errorMsg );
			}
		}
		catch (Mage_Core_Exception $e){
			Mage::getSingleton('checkout/session')->addError($e->getMessage());
			$this->_redirect('checkout/cart');
			return;
		}
    }

    public function statusAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('');
            return;
        }
		uytuytuy;
        $this->getWm()->setStatusFormData($this->getRequest()->getPost());
        $this->getWm()->statusPostSubmit();
    }
	
	public function resultAction()
	{
		if (!$this->getRequest()->isPost()) {
			$this->_redirect(''); return;
		}
		$response = $this->getRequest()->getParams();
		if ( isset($response['LMI_PREREQUEST']) )
			$preRequest = (int)$response['LMI_PREREQUEST'];
		else
			$preRequest = 0;
		if (isset($response['LMI_PAYEE_PURSE']) &&
			isset($response['LMI_PAYMENT_AMOUNT']) &&
			isset($response['LMI_PAYMENT_NO']) &&
			isset($response['SPR_PAYMENT_NO']) &&
			isset($response['LMI_PAYER_WM']) &&
			isset($response['LMI_PAYER_PURSE']) &&
			isset($response['LMI_MODE']) &&
			isset($response['RND'])) { ;
		}
		else {
			$msg = Mage::helper('webmoney')->__('Inconsistent parameters.');
			echo $msg; return;
		}

		$myPurse = $response['LMI_PAYEE_PURSE'];
		$customerAmount = $response['LMI_PAYMENT_AMOUNT'];
		$paymentNumber = $response['LMI_PAYMENT_NO'];
		$customerWmid = $response['LMI_PAYER_WM'];
		$customerPurse = $response['LMI_PAYER_PURSE'];
		$lmiMode = $response['LMI_MODE'];
		$rnd = $response['RND'];

		$order = $this->loadOrder($paymentNumber);		
		if ( !$order->getId() ) {
			echo Mage::helper('webmoney')->__('Order not found.'); return;
		}
		
		if ( $this->getWm()->validateIpAddress() ) {
			if ( $preRequest ) {
				if ( $order->getId() ) {
					try{
						$resource = Mage::getSingleton('core/resource');
						$read = $resource->getConnection('core_read');
						$wmTable = $resource->getTableName('thienha_webmoney');
						$select = $read->select();
						$select->from($wmTable,array('transaction_id','state','order_id','rnd'))
							->where('order_id=?',$paymentNumber)
							->where('rnd=?',$rnd)
							->where('state="INI"');
						$dbData = $read->fetchRow($select);
						if ( $dbData >= 1 ) {
							$totalDue = $order->getTotalDue();
							$currency_code = $order->getOrderCurrencyCode();
							$wmPurses = $this->getWm()->getWmPurses();
							$wmPurse = $wmPurses[$currency_code];
							$wmPurse = $this->getWm()->getConfigData( $wmPurse );
							if ( $wmPurse == $myPurse && $customerAmount == $totalDue ) {
								$write = $resource->getConnection('core_write');
								$condition = $write->quoteInto("transaction_id=?", $dbData['transaction_id']);
								$data = array(
									'state' => 'RES',
									'timestamp'=>'CURRENT_TIMESTAMP()'
									);
								$write->update($wmTable, $data, $condition);
								$write->query('UPDATE '.$wmTable.' SET timestamp=CURRENT_TIMESTAMP() WHERE transaction_id="'.$dbData['transaction_id'].'"');
								$this->returnData('YES');
								return;
							}
							else{
								$errMsg = Mage::helper('webmoney')->__('Inconsistent parameters.');
								Mage::logException( $errMsg );
								echo $errMsg;
							}
						}
						$errMsg = Mage::helper('webmoney')->__('Cannot find the order [1].');
						Mage::logException( $errMsg );
						echo $errMsg;
					}
					catch (Mage_Core_Exception $e){
						Mage::logException($e); 
					}				
				}
				$errMsg = Mage::helper('webmoney')->__('Cannot find the order [2].');
				Mage::logException( $errMsg );
				echo $errMsg;
			}
			else {
				try{
					$wmInvsNumber = $response['LMI_SYS_INVS_NO'];
					$wmTransaction = $response['LMI_SYS_TRANS_NO'];
					$wmTransactionDate = $response['LMI_SYS_TRANS_DATE'];
					$wmHash = $response['LMI_HASH'];
					$resource = Mage::getSingleton('core/resource');
					$read = $resource->getConnection('core_read');
					$write = $resource->getConnection('core_write');					
					$wmTable = $resource->getTableName('thienha_webmoney');
					$select = $read->select();
					$select->from($wmTable,array('transaction_id','state','order_id','rnd'))
						->where('order_id=?',$paymentNumber)
						->where('rnd=?',$rnd)
						->where('state="RES"');
					$dbData = $read->fetchRow($select);
										
					if ( $dbData >= 1 ) {
						$chkstring = $myPurse;
						$chkstring .= $customerAmount;
						$chkstring .= $paymentNumber;
						$chkstring .= $lmiMode;
						$chkstring .= $wmInvsNumber;
						$chkstring .= $wmTransaction;
						$chkstring .= $wmTransactionDate;
						$chkstring .= Mage::helper('core')->decrypt( $this->getWm()->getConfigData( 'sec_key' ) );
						$chkstring .= $customerPurse;
						$chkstring .= $customerWmid;
						
						// MD5
						$errorMsg = '';
						$md5sum = strtoupper( md5($chkstring) );
						$hash_check = ($wmHash == $md5sum);

						if ( $hash_check ) {
							$totalDue = $order->getTotalDue();
							$currency_code = $order->getOrderCurrencyCode();
							$wmPurses = $this->getWm()->getWmPurses();
							$wmPurse = $wmPurses[$currency_code];
							$wmPurse = $this->getWm()->getConfigData( $wmPurse );
							if ( $wmPurse == $myPurse && $customerAmount == $totalDue  ) {
								$condition = $write->quoteInto("transaction_id=?", $dbData['transaction_id']);
								$data = array(
									'state' => 'SUC',
									'timestamp'=>'CURRENT_TIMESTAMP()',
									'lmi_sys_invs_no' => $wmInvsNumber,
									'lmi_sys_trans_no' => $wmTransaction,
									'lmi_sys_trans_date' => $wmTransactionDate,
									'lmi_payer_purse' => $customerPurse,
									'lmi_payer_wm' => $customerWmid
									);
								$write->update($wmTable, $data, $condition);
								$write->query('UPDATE '.$wmTable.' SET timestamp=CURRENT_TIMESTAMP() WHERE transaction_id="'.$dbData['transaction_id'].'"');
								$temp = 'invs_no = '.$wmInvsNumber.' ';
								$temp .= 'trans_no = '.$wmTransaction;
								$temp .= 'trans_date = '.$wmTransactionDate;
								$order->addStatusToHistory($order->getStatus(), Mage::helper('webmoney')->__('Customer was paid Order: ').$temp);
								return;
							}
							else{
								$msg = Mage::helper('webmoney')->__('Inconsistent parameters.');
								Mage::throwException($msg);
							}						
						}
						else{
							$errorMsg = Mage::helper('webmoney')->__('Checksum is incorrect: ');
							$condition = $write->quoteInto("transaction_id=?", $dbData['transaction_id']);
							$data = array(
								'state' => 'FAL',
								'lmi_sys_invs_no' => $wmInvsNumber,
								'lmi_sys_trans_no' => $wmTransaction,
								'lmi_sys_trans_date' => $wmTransactionDate,
								'lmi_payer_purse' => $customerPurse,
								'lmi_payer_wm' => $customerWmid
								);
							$write->update($wmTable, $data, $condition);
							$write->query('UPDATE '.$wmTable.' SET timestamp=CURRENT_TIMESTAMP() WHERE transaction_id="'.$dbData['transaction_id'].'"');
							$errorMsg .= 'invs_no = '.$wmInvsNumber.' trans_no = '.$wmTransaction.' trans_date = '.$wmTransactionDate;							
							if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
								$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $errorMsg);
								$order->save();
								Mage::getSingleton('checkout/session')->unsLastRealOrderId();
							}
							echo $errorMsg;
							Mage::throwException($errorMsg);
						}					
					}
					$errorMsg = Mage::helper('webmoney')->__('Payment not found.');
					if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
						$order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $errorMsg);
						$order->cancel();
						$order->save();
					}
					echo $errorMsg;
				}
				catch (Mage_Core_Exception $e){
					Mage::logException($e);
				}
 			}			
		}
		else {
			$msg = Mage::helper('webmoney')->__('Invalid IP address: '); $msg .= $this->getWm()->getIp();
			if ( isset($response['LMI_PAYMENT_NO']) && isset($response['RND']) ) {
				$lmiPaymentNo = $response['LMI_PAYMENT_NO'];
				if ( $order->getId() ) $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $msg);
				$resource = Mage::getSingleton('core/resource');
				$read = $resource->getConnection('core_read');
				$wmTable = $resource->getTableName('thienha_webmoney');
				$select = $read->select();
				$select->from($wmTable,array('transaction_id','state','order_id','rnd'))
					->where('order_id=?',$paymentNumber)
					->where('rnd=?',$rnd);
				$dbData = $read->fetchRow($select);
				if ( $dbData >= 1 ) {					
					$write = $resource->getConnection('core_write');
					$condition = $write->quoteInto("transaction_id=?", $dbData['transaction_id']);
					$write->query('UPDATE '.$wmTable.' SET state="FAL", timestamp=CURRENT_TIMESTAMP() WHERE transaction_id="'.$dbData['transaction_id'].'"');
				}
			}
			echo $msg;
			Mage::throwException($msg);
		}
	}

    public function failureAction()
    {
        $errorMsg = Mage::helper('webmoney')->__('There was an error occurred during paying process.');

		if (!$this->getRequest()->isPost()) {
			$this->_redirect(''); return;
		}
		$response = $this->getRequest()->getParams();
		
		$lmiPaymentNo = $response['LMI_PAYMENT_NO'];
		$order = $this->loadOrder($lmiPaymentNo);
		
		if ( !$order->getId() ) {
			$this->getCheckout()->unsLastRealOrderId();		
			$this->loadLayout();
			$this->renderLayout();
			$this->_redirect('checkout/cart');
			return;		
		}
		
		try{
			$resource = Mage::getSingleton('core/resource');
			$read = $resource->getConnection('core_read');
			$wmTable = $resource->getTableName('thienha_webmoney');
			$select = $read->select();
			$select->from($wmTable,array('transaction_id','state','order_id','rnd'))
				->where('order_id=?',$lmiPaymentNo);
			$dbData = $read->fetchRow($select);
			if ( $dbData >= 1 ) {
				if ( $dbData['rnd'] == $response['RND'] ) {
					$write = $resource->getConnection('core_write');
					$condition = $write->quoteInto("transaction_id=?", $dbData['transaction_id']);
					$timestamp = 'CURRENT_TIMESTAMP()';
					$data = array(
						'state' => 'FAL',
						'timestamp' => $timestamp,
						'lmi_sys_invs_no' => $response['LMI_SYS_INVS_NO'],
						'lmi_sys_trans_no' => $response['LMI_SYS_TRANS_NO'],
						'lmi_sys_trans_date' => $response['LMI_SYS_TRANS_DATE']				
						);
					$write->update($wmTable, $data, $condition);
					$write->query('UPDATE '.$wmTable.' SET timestamp=CURRENT_TIMESTAMP() WHERE transaction_id="'.$dbData['transaction_id'].'"');
				}
				else {
					Mage::logException( '$dbData["rnd"] != $response["RND"]' );
				}
			}
			else {
				Mage::logException( '$dbData >= 1 filed' );
			}
		}
		catch (Mage_Core_Exception $e){
			Mage::logException($e); 
		}		
		
        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            $order->addStatusToHistory(Mage_Sales_Model_Order::STATE_CANCELED, $errorMsg);
            $order->cancel();
            $order->save();
        }
    	
        $this->getCheckout()->unsLastRealOrderId();		
        $this->loadLayout();
        $this->renderLayout();
		$this->_redirect('checkout/cart');
    }
	
}