<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Workiverse
 * @package     Workiverse_Perfectmoney
 * @copyright   Copyright (c) 2012 Workiverse Org (http://www.forummods.org)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Workiverse_Perfectmoney_Block_Jsinit extends Mage_Adminhtml_Block_Template
{
    /**
     * Include JS in head if section is perfectmoney
     */
    protected function _prepareLayout()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'perfectmoney') {
            $this->getLayout()
                ->getBlock('head');
        }
        parent::_prepareLayout();
    }

    /**
     * Print init JS script into body
     * @return string
     */
    protected function _toHtml()
    {
        $section = $this->getAction()->getRequest()->getParam('section', false);
        if ($section == 'perfectmoney') {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
