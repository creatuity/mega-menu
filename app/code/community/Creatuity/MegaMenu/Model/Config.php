<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Config
{

    const MEGA_MENU_TAB_NAME = 'Megamenu';

    public function isModuleEnabled()
    {
        return (bool) Mage::getStoreConfig('creatuity_megamenu/general/module_enabled') && Mage::helper('core')->isModuleEnabled('Creatuity_MegaMenu');
    }

    public function defaultCategoryType()
    {
        return (string) Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Type::LINK;
    }

    public function mobileSize()
    {
        return (int) Mage::getStoreConfig('creatuity_megamenu/general/mobile_size');
    }

    public function isForceWorkOnClick()
    {
        return (bool) Mage::getStoreConfig('creatuity_megamenu/general/force_click');
    }

    public function isBreadcumbLinkToCategory()
    {
        return (int) Mage::getStoreConfig('creatuity_megamenu/general/breadcrumb_link');
    }

    public function thumbnailWidth()
    {
        return (int) Mage::getStoreConfig('creatuity_megamenu/thumbnail/width');
    }

    public function thumbnailHeight()
    {
        return (int) Mage::getStoreConfig('creatuity_megamenu/thumbnail/height');
    }

    public function widgetSliderImageWidth()
    {
        return (int) Mage::getStoreConfig('creatuity_megamenu/slider_image/width');
    }

    public function widgetSliderImageHeight()
    {
        return (int) Mage::getStoreConfig('creatuity_megamenu/slider_image/height');
    }

    public function maxWidgetItemsCount()
    {
        return (int) 20;
    }

    public function defaultAttributeValue($attrCode)
    {
        $vals = $this->defaultAttributeValues();
        return isset($vals[$attrCode]) ? $vals[$attrCode] : null;
    }

    public function defaultAttributeValues()
    {
        return $this->_megamenuAttributes();
    }

    public function isMegaMenuAttribute($attrCode)
    {
        return array_key_exists($attrCode, $this->_megamenuAttributes());
    }

    public function megaMenuAttributeCodes()
    {
        return array_keys($this->_megamenuAttributes());
    }

    protected function _megamenuAttributes()
    {
        return array(
            'megamenu_type' => $this->defaultCategoryType(),
            'megamenu_cms_block' => Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_CmsBlock::NONE,
            'megamenu_optlevel_oneverypage' => Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Optimization::CHILDS_LVL1,
            'megamenu_optlevel_onajaxcalls' => Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Optimization::CHILDS_LVL1,
            'megamenu_frontend_class' => '',
        );
    }

    public function getCacheTags()
    {
        return array(
            Mage_Catalog_Model_Category::CACHE_TAG,
            Mage_Core_Model_Store_Group::CACHE_TAG,
            Mage_Cms_Model_Block::CACHE_TAG,
            Mage_Core_Model_Config::CACHE_TAG,
        );
    }

}
