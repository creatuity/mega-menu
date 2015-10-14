<?php

class Creatuity_MegaMenu_Block_Initializer extends Mage_Core_Block_Template
{

    public function getJsonConfig()
    {
        $cacheParam = $this->_cache()->getFuzeParamName();
        return Mage::helper('core')->jsonEncode(array(
                    'force_work_on_click' => $this->_config()->isForceWorkOnClick(),
                    'mobile_mode_size' => $this->_config()->mobileSize(),
                    'controller_path' => Mage::getUrl('megamenu/megamenu/index'),
                    'breadcrumbs_links_to_category' => $this->_config()->isBreadcumbLinkToCategory(),
                    $cacheParam => $this->_cache()->getFuzeParamValue(),
                    'all_categories_mode' => $this->_allCategoriesMode(),
                    'current_category_id' => $this->_currentCategoryId()
        ));
    }

    protected function _allCategoriesMode()
    {
        $rootCategory = $this->_mmCategoryHelper()->getRootCategoryId();
        $categories = $this->_mmCategoryHelper()->loadCategoryChildren($rootCategory);
        return $this->_optimization()->shouldLoadAll($categories, "onajaxcalls");
    }

    protected function _currentCategoryId()
    {
        return Mage::registry('current_category') ? Mage::registry('current_category')->getId() : null;
    }

    /**
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getSingleton('creatuity_megamenu/config');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Cache
     */
    protected function _cache()
    {
        return Mage::getSingleton('creatuity_megamenu/cache');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Helper
     */
    protected function _mmCategoryHelper()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_helper');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Optimization
     */
    protected function _optimization()
    {
        return Mage::getSingleton('creatuity_megamenu/optimization');
    }

}
