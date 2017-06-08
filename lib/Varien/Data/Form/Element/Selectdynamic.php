<?php
/**
 * SMS Notifier
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 *
 * @category    Artio
 * @package     Artio_SMSNotifier
 * @copyright   Copyright (c) 2013 Artio s.r.o (http://www.artio.net/)
 * @license     http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

/**
 * Form text element
 *
 * @category   Varien
 * @package    Varien_Data
 * @author     Artio Magento Team <info@artio.net>
 */
class Varien_Data_Form_Element_SelectDynamic extends Varien_Data_Form_Element_Select
{

	public function getAfterElementHtml()
	{
		$html = parent::getAfterElementHtml();

		$html .= '<input type="hidden" id="'.$this->getHtmlId().'-hidden" value="'.$this->getValue().'" />';

		return $html;
	}

}