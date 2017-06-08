<?php
class Magehit_Storecredit_Adminhtml_CustomerController extends Mage_Adminhtml_Controller_Action
{
 
    public function preDispatch()
    {
        parent::preDispatch();
        return $this;
    }

    /**
     * History Ajax Action
     */
    public function historyAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $history = $this->getLayout()
            ->createBlock('storecredit/adminhtml_customer_edit_tab_storecredit_history', '',
                array('customer_id' => $customerId));
        $this->getResponse()->setBody($history->toHtml());
    }

    /**
     * History Grid Ajax Action
     *
     */
    public function historyGridAction()
    {
        $customerId = $this->getRequest()->getParam('id', 0);
        $history = $this->getLayout()
            ->createBlock('storecredit/adminhtml_customer_edit_tab_storecredit_history_grid', '',
                array('customer_id' => $customerId));
        $this->getResponse()->setBody($history->toHtml());
    }
    protected function _isAllowed()
    {
    	return true;
    }
}
