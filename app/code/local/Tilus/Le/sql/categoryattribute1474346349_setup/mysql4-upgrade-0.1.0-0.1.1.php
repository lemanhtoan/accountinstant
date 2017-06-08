<?php
/**
 *
 * @author  Paul Hachmang (@paales)
 * @author Ashley Schroder (aschroder.com)
 * @copyright  Copyright (c) 2014 Ashley Schroder
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


$installer = $this;

$installer->startSetup();

$installer->addAttribute("catalog_category", "category_registerlink",  array(
    "type"     => "text",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Category Register link",
    "input"    => "text",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
    "wysiwyg_enabled"   => 1,
    "is_html_allowed_on_front" => true,
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => ""

	));
$installer->endSetup();
	 
