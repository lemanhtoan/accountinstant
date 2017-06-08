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
 * @copyright   Copyright (c) 2013 Artio (http://www.artio.net)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Enhanced select
 *
 * @category    Artio
 * @package     Artio_SMSNotifier
 * @author      Artio Magento Team (info@artio.net)
 */
class Varien_Data_Form_Element_Selectex extends Varien_Data_Form_Element_Select
{
   /* public function __construct($attributes=array())
    {
        parent::__construct($attributes);
        $this->setType('select');
        $this->setExtType('combobox');
        $this->_prepareOptions();
    }*/

    public function getElementHtml()
    {
        $this->addClass('select');
        $html = '<select id="'.$this->getHtmlId().'" name="'.$this->getName().'" '.$this->serialize($this->getHtmlAttributes()).'>'."\n";

        $value = $this->getValue();
        if (!is_array($value)) {
            $value = array($value);
        }

        if ($values = $this->getValues()) {
            foreach ($values as $key => $option) {
                if (is_array($option['value'])) {
                    $html.='<optgroup label="'.$option['label'].'">'."\n";
                    foreach ($option['value'] as $groupItem) {
                        $html.= $this->_optionToHtml($groupItem, $value);
                    }
                    $html.='</optgroup>'."\n";
                }
                else {
                    $html.= $this->_optionToHtml($option, $value);
                }
            }
        }

        $html.= '</select>'."\n";
        $html.= $this->getAfterElementHtml();
        return $html;
    }

    protected function _optionToHtml($option, $selected)
    {
        if (is_array($option['value'])) {
            $html ='<optgroup label="'.$option['label'].'">'."\n";
            foreach ($option['value'] as $groupItem) {
                $html .= $this->_optionToHtml($groupItem, $selected);
            }
            $html .='</optgroup>'."\n";
        }
        else {
            $html = '<option value="'.$this->_escape($option['value']).'"';
            $html.= isset($option['title']) ? 'title="'.$this->_escape($option['title']).'"' : '';
            $html.= isset($option['style']) ? 'style="'.$option['style'].'"' : '';
            $html.= isset($option['disabled']) ? 'disabled="'.$option['disabled'].'"' : '';
            if (in_array($option['value'], $selected)) {
                $html.= ' selected="selected"';
            }
            $html.= '>'.$this->_escape($option['label']). '</option>'."\n";
        }
        return $html;
    }

    protected function _prepareOptions()
    {
        $values = $this->getValues();
        if (empty($values)) {
            $options = $this->getOptions();

            if (is_array($options)) {
                $values = array();
                foreach ($options as  $value => $label) {
                    $values[] = $label;
                }
            } elseif (is_string($options)) {
                $values = array( array('value' => $options, 'label' => $options) );
            }
            $this->setValues($values);
        }
    }

    public function getHtmlAttributes()
    {
        return array('title', 'class', 'style', 'onclick', 'onchange', 'disabled', 'readonly', 'tabindex');
    }
}