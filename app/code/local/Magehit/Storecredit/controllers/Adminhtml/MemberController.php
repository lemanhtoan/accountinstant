<?php

class Magehit_Storecredit_Adminhtml_MemberController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('storecredit/storecredit')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Items Manager'), Mage::helper('adminhtml')->__('Item Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		
		$id     =  $this->getRequest()->getParam('id');
		Mage::helper('storecredit')->checkAndInsertCustomerId($id);
		
		$model  = Mage::getModel('storecredit/customer')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('storecredit_data_member', $model);

			$this->loadLayout();
			$this->_setActiveMenu('storecredit/storecredit');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('storecredit/adminhtml_member_edit'))
				->_addLeft($this->getLayout()->createBlock('storecredit/adminhtml_member_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storecredit')->__('Member does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function transactionAction()
	{
        $this->getResponse()->setBody($this->getLayout()->createBlock('storecredit/adminhtml_member_edit_tab_transaction')->toHtml());
	}
	public function saveAction() {
		$data = $this->getRequest()->getPost();
		if ($data) {
		    $data = $this->getRequest()->getPost();
			$member_id = $this->getRequest()->getParam('id');
			try {	
				if($member_id!=''){	
					 
					 $_customer = Mage::getModel('storecredit/customer')->load($member_id);
					 $store_id = Mage::getModel('customer/customer')->load($_customer->getId())->getStoreId();
    				 $oldCredit = $_customer->getCreditBalance();
    				 $amount = $data['mh_storecredit_amount'];
			    	 $action = $data['mh_storecredit_action'];
			    	 $comment = $data['mh_storecredit_comment'];
			    	 $newCredit = $oldCredit + $amount * $action;
			    	 
					if($newCredit < 0) $newCredit = 0;
			    	$amount = abs($newCredit - $oldCredit);
			    	
			    	if($amount > 0){
				    	$detail = $comment;
						$_customer->setData('credit_balance',$newCredit)->save();
				    	$balance = $_customer->getCreditBalance();
				    	
					
				    	$historyData = array('customer_id'=>$member_id,
				    					     'transaction_type'=>($action>0)?Magehit_Storecredit_Model_Type::ADMIN_ADDITION:Magehit_Storecredit_Model_Type::ADMIN_SUBTRACT, 
									    	 'amount'=>$amount,
									    	 'balance'=>$balance, 
									    	 'transaction_params'=>$detail,
				    	                     'transaction_detail'=>$detail,
				    	                     'order_id'=>0,
									    	 'transaction_time'=>now(), 
	    									 'expired_time'=>null,
		            						 'remaining_credit'=>0,
									    	 'status'=>Magehit_Storecredit_Model_Statushistory::COMPLETE);
				    	
				    	Mage::getModel('storecredit/history')->setData($historyData)->save();
				    	
						Mage::helper('storecredit')->sendEmailCustomerCreditChanged($_customer->getId(),$historyData, $store_id);
			    	}
    				 
				}	
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('storecredit')->__('The member has successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);
				
				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('storecredit')->__('Unable to find member to save'));
        $this->_redirect('*/*/');
	}

    public function exportCsvAction()
    {
        $fileName   = 'storecredit_member.csv';
        $content    = $this->getLayout()->createBlock('storecredit/adminhtml_member_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'storecredit_member.xml';
        $content    = $this->getLayout()->createBlock('storecredit/adminhtml_member_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}