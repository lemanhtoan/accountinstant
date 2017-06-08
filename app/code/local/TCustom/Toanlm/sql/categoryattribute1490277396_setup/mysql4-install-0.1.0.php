<?php
$installer = $this;
$installer->startSetup();


$installer->addAttribute("catalog_category", "add_blogs",  array(
    "type"     => "text",
    "backend"  => "toanlm/eav_entity_attribute_backend_categoryoptions14902773960",
    "frontend" => "",
    "label"    => "Blog include",
    "input"    => "multiselect",
    "class"    => "",
    "source"   => "toanlm/eav_entity_attribute_source_categoryoptions14902773960",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => "Blog include"

	));

$installer->addAttribute("catalog_category", "add_blog_descip",  array(
    "type"     => "text",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Blog desciption",
    "input"    => "textarea",
    "class"    => "",
    "source"   => "",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => "Blog desciption"

	));

$installer->addAttribute("catalog_category", "add_blog_bestpro",  array(
    "type"     => "int",
    "backend"  => "",
    "frontend" => "",
    "label"    => "Best product choice",
    "input"    => "select",
    "class"    => "",
    "source"   => "toanlm/eav_entity_attribute_source_categoryoptions14902773962",
    "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    "visible"  => true,
    "required" => false,
    "user_defined"  => false,
    "default" => "",
    "searchable" => false,
    "filterable" => false,
    "comparable" => false,
	
    "visible_on_front"  => false,
    "unique"     => false,
    "note"       => "Best product choice"

	));
$installer->endSetup();
	 