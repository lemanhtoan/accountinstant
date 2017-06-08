<?php
class Magestore_Imageoption_Adminhtml_TemplateController extends Mage_Adminhtml_Controller_Action
{
	protected function _initAction() 
	{
		$this->loadLayout()
			->_setActiveMenu('catalog/imageoptiontemplate')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Option Templates Manager'), Mage::helper('adminhtml')->__('Option Templates Manager'));
		
		return $this;
	}
	
	public function indexAction() 
	{
		$this->_initAction()
			->renderLayout();
	}
	
	public function selecttemplateAction()
	{
		$html = '';
		
		$template_id = $this->getRequest()->getParam('template_id');
		
		$template = Mage::getModel('imageoption/template')->load($template_id);
		
		if($template)
			$html = $template->getShortDescrip();
		
		$this->getResponse()->setHeader('Content-type', 'application/x-json');
        $this->getResponse()->setBody($html);			
	}
	
	public function editAction() 
	{
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('imageoption/template')->load($id);
		$store_id = $this->getRequest()->getParam('store');
		$store_id = $store_id ? $store_id : 0;
		
		Mage::getSingleton('core/session')->setData('is_search',0);
		
		$product = Mage::getModel('catalog/product');
		$product->setId(0);
		$product->setStoreId($store_id);		
		Mage::register('product', $product);		
		
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}
			
			Mage::register('template_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('catalog/imageoptiontemplate');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Option Templates Manager'), Mage::helper('adminhtml')->__('Option Templates Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Option Template News'), Mage::helper('adminhtml')->__('Option Template News'));


			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true)->setCanLoadRulesJs(true);
			
			$this->_addContent($this->getLayout()->createBlock('imageoption/adminhtml_template_edit'))
				->_addLeft($this->getLayout()->createBlock('imageoption/adminhtml_template_edit_tabs'));

			$this->renderLayout();
			
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('imageoption')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}  	
 
	public function newAction() {
		$this->_forward('edit');
	}

	public function saveAction()
	{	
		$data = $this->getRequest()->getPost();

		$store_id = $this->getRequest()->getParam('store');
		$store_id = $store_id ? $store_id : 0;
			// prepare options
		if(isset($data['product']['options']))
		{
			$options = $data['product']['options'];
			$product = Mage::getModel('catalog/product');
			$product->setId(0);
			$product->setStoreId($store_id);
		
			$option_model = Mage::getModel('imageoption/productoption');
			$option_model->setProduct($product);
			//$option_model->setData('store_id',$this->getRequest()->getParam('store'));
			$option_model->setOptions($options);
		}
			//prepare product_ids
		if(isset($data['link_products']))
		{
			$productIds = array();
            parse_str($data['link_products'], $productIds);
			$productIds = array_keys($productIds);
		}else{
			$productIds = array(0);
		}
			//prepare template
		$data['status'] = $data['template_status']; 
		$template = Mage::getModel('imageoption/template')->setData($data)
						->setId($this->getRequest()->getParam('id'));
		
		try{
				//save template
			$template->save();				
				
				//check options
			if(isset($option_model))
			{
					//save options
				$optionsIds = $option_model->saveOptions();
			
				if(count($optionsIds))
				{
					//save optiontemplate
					foreach($optionsIds as $option_id)
					{
					
						$optiontemplate = Mage::getModel('imageoption/optiontemplate')
											->loadByTempIdOpId($template->getId(),$option_id);
											
						$optiontemplate->setData('template_id',$template->getId());
						$optiontemplate->setData('option_id',$option_id);
						
						$optiontemplate->save();
						$optiontemplate->setId(null);
					}
				}
			}
				//update product template
			if(isset($data['link_products']))
			{				
				if(isset($productIds) && count($productIds))
				{
						//delete
					$collection = Mage::getModel('imageoption/producttemplate')
								->getCollection()
								->addFieldToFilter('product_id',array('nin'=>$productIds));
					if(count($collection))
					foreach($collection as $item)
					{
						$item->delete();
					}
						//update
					foreach($productIds as $product_id)
					{
						if(intval($product_id) != 0)
						{
							$producttemplate = Mage::getModel('imageoption/producttemplate')
									->loadByTempIdPrdId($template->getId(),$product_id);				
						
							$producttemplate->setData('template_id',$template->getId());
							$producttemplate->setData('product_id',$product_id);
							$producttemplate->save();
							$producttemplate->setId(null);
						}
					}
				}
			}
				//apply template
			if ($this->getRequest()->getParam('apply')) 
			{
				$this->applyAction($template);
				return;
			}
			
			if ($this->getRequest()->getParam('back')) 
			{
				$this->_redirect('*/*/edit', array('id' => $template->getId(),'store'=>$this->getRequest()->getParam('store')));
				return;
			}
			$this->_redirect('*/*/index',array());
			return;
		} catch(Exception $e) {
			Mage::getSingleton('core/session')->addError($e->getMessage());
			$this->_redirect('*/*/edit',array('id'=>$this->getRequest()->getParam('id')));
			return;
		}
	}
	
	public function applyAction($template)
	{
		$templateId = $template->getId();
		$productIds = $template->getProductIds();
		try{
		if(count($productIds))
		{
				//apply template for product
			foreach($productIds as $product_id)
			{
				$producttemplate = Mage::getModel('imageoption/producttemplate')
							->loadByTempIdPrdId($templateId,$product_id);				
				
				if($producttemplate->getApplied() == 0)
				{
					$producttemplate->apply();
					$producttemplate->setApplied(1);
					$producttemplate->save();
					$producttemplate->setId(null);
				}
			}
		}
			$this->_redirect('*/*/index',array());
			return;
			
		} catch(Exception $e){
			Mage::getSingleton('core/session')->addError($e->getMessage());
			$this->_redirect('*/*/edit',array('id'=>$this->getRequest()->getParam('id')));
			return;		
		}
	}
	
    public function optionsAction()
    {
		$html = $this->getLayout()->createBlock('imageoption/adminhtml_template_edit_tab_options', 'admin.product.options')->toHtml();
	
        $this->getResponse()->setBody($html);
    }

    public function listproductAction()
    {
		Mage::getSingleton('core/session')->setData('is_search',false);        
		$this->loadLayout();
        $this->getLayout()->getBlock('imageoption.template.listproduct')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }	
	
    public function listproductGridAction()
    {
        $this->loadLayout();
        $this->getLayout()->getBlock('imageoption.template.listproduct')
            ->setProductsRelated($this->getRequest()->getPost('products_related', null));
        $this->renderLayout();
    }		


	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('imageoption/template');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Template was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $templateIds = $this->getRequest()->getParam('template');
        if(!is_array($templateIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Template(s)'));
        } else {
            try {
                foreach ($templateIds as $templateId) {
                    $template = Mage::getModel('imageoption/template')->load($templateId);
                    $template->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($templateIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $templateIds = $this->getRequest()->getParam('template');
        if(!is_array($templateIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select template(s)'));
        } else {
            try {
                foreach ($templateIds as $templateId) {
                    $imageoption = Mage::getSingleton('imageoption/template')
                        ->load($templateId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($templateIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'template.csv';
        $content    = $this->getLayout()->createBlock('imageoption/adminhtml_template_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'template.xml';
        $content    = $this->getLayout()->createBlock('imageoption/adminhtml_template_grid')
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