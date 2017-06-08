<?php 
class Magehit_Storecredit_Block_Adminhtml_Customer_Edit_Tab_Storecredit_History
    extends Mage_Adminhtml_Block_Template
{
    /**
     * Internal constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('mh_storecredit/customer/edit/history.phtml');
    }

    /**
     * Prepare layout.
     * Create history grid block
     *
     * @return Enterprise_Reward_Block_Adminhtml_Customer_Edit_Tab_Reward_History
     */
    protected function _prepareLayout()
    {
        $grid = $this->getLayout()
            ->createBlock('storecredit/adminhtml_customer_edit_tab_storecredit_history_grid')
            ->setCustomerId($this->getCustomerId());
        $this->setChild('grid', $grid);
        return parent::_prepareLayout();
    }
}
