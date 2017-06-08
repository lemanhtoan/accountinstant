<?php
class Websmart_ManageKeys_Adminhtml_ManagekeysController extends Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Websmart_ManageKeys_Adminhtml_ManagekeysController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('managekeys/managekeys')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Items Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
        return $this;
    }
 
    /**
     * index action
     */
    public function indexAction()
    {
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * view and edit item action
     */
    public function editAction()
    {
        $managekeysId     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('managekeys/managekeys')->load($managekeysId);

        if ($model->getId() || $managekeysId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('managekeys_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('managekeys/managekeys');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item Manager'),
                Mage::helper('adminhtml')->__('Item Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Item News'),
                Mage::helper('adminhtml')->__('Item News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('managekeys/adminhtml_managekeys_edit'))
                ->_addLeft($this->getLayout()->createBlock('managekeys/adminhtml_managekeys_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('managekeys')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }
	
	public function listproductAction()
	{
        $this->loadLayout();
        $this->getLayout()->getBlock('managekeys.edit.tab.listproduct')
            ->setProduct($this->getRequest()->getPost('aproduct', null));
        $this->renderLayout();	
	}
    public function listproductGridAction()
	{
        $this->loadLayout();
        $this->getLayout()->getBlock('managekeys.edit.tab.listproduct')
            ->setProduct($this->getRequest()->getPost('aproduct', null));
        $this->renderLayout();		
	} 
	
	 public function changeproductAction()
	{
		$product_id = $this->getRequest()->getParam('product_id');
		if($product_id)
		{
			$product = Mage::getModel('catalog/product')->load($product_id);
			$product_name = $product->getName();
			$product_name = str_replace('"','',$product_name);
			$product_name = str_replace("'",'',$product_name);
			$html = '<input type="hidden" id="newproduct_name" name="newproduct_name" value="'. $product_name .'" >';
			$this->getResponse()->setHeader('Content-type', 'application/x-json');
			$this->getResponse()->setBody($html);				
		}
	}
 
    public function newAction()
    {
        $this->_forward('edit');
    }
 
    /**
     * save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
              
            $model = Mage::getModel('managekeys/managekeys'); 
			if(isset($data['candidate_product_id']) && $data['candidate_product_id'])
			{
                $data['product_id'] = $data['candidate_product_id'];
			}
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            
            try {
                if ($model->getCreatedTime() == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('managekeys')->__('Item was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
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
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('managekeys')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }
 
    /**
     * delete item action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('managekeys/managekeys');
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * mass delete item(s) action
     */
    public function massDeleteAction()
    {
        $managekeysIds = $this->getRequest()->getParam('managekeys');
        if (!is_array($managekeysIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($managekeysIds as $managekeysId) {
                    $managekeys = Mage::getModel('managekeys/managekeys')->load($managekeysId);
                    $managekeys->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Total of %d record(s) were successfully deleted',
                    count($managekeysIds))
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * mass change status for item(s) action
     */
    public function massStatusAction()
    {
        $managekeysIds = $this->getRequest()->getParam('managekeys');
        if (!is_array($managekeysIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($managekeysIds as $managekeysId) {
                    $key=Mage::getSingleton('managekeys/managekeys')
                        ->load($managekeysId)
					    ->setStatus($this->getRequest()->getParam('status'))
						->setUpdateTime(now())
                        ->save(); 
					
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($managekeysIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName   = 'managekeys.csv';
        $content    = $this->getLayout()
                           ->createBlock('managekeys/adminhtml_managekeys_grid')
                           ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName   = 'managekeys.xml';
        $content    = $this->getLayout()
                           ->createBlock('managekeys/adminhtml_managekeys_grid')
                           ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('managekeys');
    }
}