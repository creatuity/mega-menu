<?php

/* @var $installer Creatuity_MegaMenu_Model_Resource_Setup */ 
$installer = $this;


$template1 = <<<TEMPLATE1
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="1" to_numeric="6"}}
{{widget type="creatuity_megamenu/widgets_slider" scope_type="numeric" from_numeric="1" to_numeric="-1" is_including_categories_images="no" is_including_categories_products_images="yes" is_linking_enabled="yes" is_showing_product_names="yes" is_showing_product_prices="yes" max_items_count="20" image_width="600" image_height="400" is_using_advanced_parameters="no"}}
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="7" to_numeric="12"}}
{{widget type="creatuity_megamenu/widgets_slider" scope_type="numeric" from_numeric="1" to_numeric="-1" is_including_categories_images="no" is_including_categories_products_images="yes" is_linking_enabled="yes" is_showing_product_names="yes" is_showing_product_prices="yes" max_items_count="20" image_width="600" image_height="400" is_using_advanced_parameters="no"}}
TEMPLATE1;

$template2 = <<<TEMPLATE2
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="1" to_numeric="6"}}
{{widget type="creatuity_megamenu/widgets_slider" scope_type="percent" to_percent="30" is_including_categories_images="no" is_including_categories_products_images="yes" is_linking_enabled="yes" is_showing_product_names="no" is_showing_product_prices="no" max_items_count="20" image_width="600" image_height="400" is_using_advanced_parameters="no"}}
{{widget type="creatuity_megamenu/widgets_slider" scope_type="percent" to_percent="30" is_including_categories_images="no" is_including_categories_products_images="yes" is_linking_enabled="yes" is_showing_product_names="no" is_showing_product_prices="no" max_items_count="20" image_width="600" image_height="400" is_using_advanced_parameters="no"}}
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="7" to_numeric="12"}}
TEMPLATE2;

$template3 = <<<TEMPLATE3
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="1" to_numeric="8"}}
{{widget type="creatuity_megamenu/widgets_slider" scope_type="percent" to_percent="30" is_including_categories_images="no" is_including_categories_products_images="yes" is_linking_enabled="yes" is_showing_product_names="no" is_showing_product_prices="no" max_items_count="20" image_width="650" image_height="200" is_using_advanced_parameters="no"}}
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="9" to_numeric="16"}}
TEMPLATE3;

$template4 = <<<TEMPLATE4
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="1" to_numeric="6"}}
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="7" to_numeric="12"}}
<div class="megamenu-text-widget medium">
<h2>Lorem Impsum</h2>
<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
</div>
TEMPLATE4;

$template5 = <<<TEMPLATE5
{{widget type="creatuity_megamenu/widgets_links" scope_type="numeric" from_numeric="1" to_numeric="5"}}
<div class="megamenu-text-widget medium">
<h2>Lorem Impsum</h2>
<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>
</div>
{{widget type="creatuity_megamenu/widgets_slider" scope_type="percent" to_percent="30" is_including_categories_images="no" is_including_categories_products_images="yes" is_linking_enabled="yes" is_showing_product_names="yes" is_showing_product_prices="yes" max_items_count="20" image_width="550" image_height="200" is_using_advanced_parameters="no"}}
TEMPLATE5;

$template6 = <<<TEMPLATE6
{{widget type="creatuity_megamenu/widgets_gallery" scope_type="percent" to_percent="100" is_including_categories_images="no" is_including_categories_products_images="no" is_linking_enabled="yes" is_showing_product_names="yes" is_showing_product_prices="yes" max_items_count="20" image_width="180" image_height="180" is_using_advanced_parameters="no"}}
TEMPLATE6;

$cmsBlock1 = Mage::getModel('cms/block')->load('megamenu-block-1', 'identifier');
/** @var $headerLinksBlock Mage_Cms_Model_Block */
$cmsBlock1->setTitle('MegaMenu style 1')
        ->setIdentifier('megamenu-block-1')
        ->setContent($template1)
        ->setIsActive(true)
        ->setStores(array(0))
        ->save();

$cmsBlock2 = Mage::getModel('cms/block')->load('megamenu-block-2', 'identifier');
/** @var $headerLinksBlock Mage_Cms_Model_Block */
$cmsBlock2->setTitle('MegaMenu style 2')
        ->setIdentifier('megamenu-block-2')
        ->setContent($template2)
        ->setIsActive(true)
        ->setStores(array(0))
        ->save();

$cmsBlock3 = Mage::getModel('cms/block')->load('megamenu-block-3', 'identifier');
/** @var $headerLinksBlock Mage_Cms_Model_Block */
$cmsBlock3->setTitle('MegaMenu style 3')
        ->setIdentifier('megamenu-block-3')
        ->setContent($template3)
        ->setIsActive(true)
        ->setStores(array(0))
        ->save();

$cmsBlock4 = Mage::getModel('cms/block')->load('megamenu-block-4', 'identifier');
/** @var $headerLinksBlock Mage_Cms_Model_Block */
$cmsBlock4->setTitle('MegaMenu style 4')
        ->setIdentifier('megamenu-block-4')
        ->setContent($template4)
        ->setIsActive(true)
        ->setStores(array(0))
        ->save();

$cmsBlock5 = Mage::getModel('cms/block')->load('megamenu-block-5', 'identifier');
/** @var $headerLinksBlock Mage_Cms_Model_Block */
$cmsBlock5->setTitle('MegaMenu style 5')
        ->setIdentifier('megamenu-block-5')
        ->setContent($template5)
        ->setIsActive(true)
        ->setStores(array(0))
        ->save();

$cmsBlock6 = Mage::getModel('cms/block')->load('megamenu-block-6', 'identifier');
/** @var $headerLinksBlock Mage_Cms_Model_Block */
$cmsBlock6->setTitle('MegaMenu style 6(Gallery)')
        ->setIdentifier('megamenu-block-6')
        ->setContent($template6)
        ->setIsActive(true)
        ->setStores(array(0))
        ->save();


