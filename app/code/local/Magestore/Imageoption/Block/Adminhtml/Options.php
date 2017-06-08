<?php


class Magestore_Imageoption_Block_Adminhtml_Options extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Options
{
    public function __construct()
    {
        parent::__construct();
		$this->setTemplate('imageoption/options.phtml');
    }

    public function getTemplateDropHtml()
    {
       $templates = Mage::getModel('imageoption/template')->getTmplByPrdId($this->getRequest()->getParam('id'));
	   $html ='';
	   $desciptions = '';
	   if(count($templates))
	   {
			$html .= '<select name="optiontemplate_id" id="optiontemplate_id" onchange="optionTemplate.selecttemplate();">';
			$html .= '<option value="">'. $this->__('Select template') .'</option>';
			foreach($templates as $tmpl)
			{
				$html .= '<option value='. $tmpl->getId() .'>'. $tmpl->getTitle() .'</option>';
				$desciptions .= '<div class="description-templ" style="display:none;" id="description-templ-'.$tmpl->getId().'">'.$tmpl->getDescription().'</div>';
			}
			$html .= '</select>'. $desciptions;
	   }
	   return $html;
    }

}