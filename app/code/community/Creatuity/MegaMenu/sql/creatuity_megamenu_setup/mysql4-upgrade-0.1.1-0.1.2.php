<?php
/* @var $installer Creatuity_MegaMenu_Model_Resource_Setup */ 
$installer = $this;

$installer->addCategoryAttribute(
    "megamenu_type", 
    array(
        "type"     => "varchar",
        "backend"  => "",
        "frontend" => "",
        "label"    => "Mega-Menu Type",
        "input"    => "select",
        "frontend_class"    => "megamenu-type",
        "source"   => "creatuity_megamenu/catalog_category_attribute_megamenu_source_type",
        "global"   => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'group'    => Creatuity_MegaMenu_Model_Config::MEGA_MENU_TAB_NAME,
        "visible"  => true,
        "required" => false,
        "user_defined"  => false,
        "default" => null,
        "sort_order" => 1,
        "searchable" => false,
        "filterable" => false,
        "comparable" => false,
        "visible_on_front"  => false,
        "unique"     => false,
        "note"       => ""
));

