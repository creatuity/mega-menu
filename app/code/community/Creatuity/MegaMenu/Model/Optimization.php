<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 * 
 */
class Creatuity_MegaMenu_Model_Optimization extends Varien_Object
{

    public function loadRelatedCategoriesForRootCategories(array $rootCategoriesOrIds, $allMode = false)
    {
        if ($allMode || $this->shouldLoadAll($rootCategoriesOrIds, "oneverypage")) {
            return $this->_loadAllCategories();
        }

        $rootCategoiresOrIdsToInclude = array();
        $levels = array();
        foreach ($rootCategoriesOrIds as $baseCategoryOrId) {
            $lvl = $this->_categoryLevelDepth($baseCategoryOrId, "oneverypage");
            if ($lvl > 0) {
                $cat = $this->_mmCategoryHelper()->asCategory($baseCategoryOrId);
                $rootCategoiresOrIdsToInclude[$cat->getId()] = $cat;
            }
            $levels[] = max(0, $lvl - 1);
        }

        return $rootCategoiresOrIdsToInclude + $this->_mmCategoryHelper()->loadCategoriesChildren($rootCategoriesOrIds, $levels, true);
    }

    public function loadRelatedCategoriesForAjaxCall(array $toCategoriesOrIds, $allMode)
    {
        if ($allMode || $this->shouldLoadAll($toCategoriesOrIds, "onajaxcalls")) {
            return $this->_loadAllCategories();
        }

        $levels = array();
        foreach ($toCategoriesOrIds as $baseCategoryOrId) {
            $levels[] = $this->_categoryLevelDepth($baseCategoryOrId, "onajaxcalls");
        }
        return $this->_mmCategoryHelper()->loadCategoriesChildren($toCategoriesOrIds, $levels, true);
    }

    protected function _categoryLevelDepth($categoryOrId, $optType)
    {
        $optLevel = $this->_mmCategoryHelper()->getCategoryOptimization($categoryOrId, $optType);
        return $this->_mmOptimization()->toChildrenLevelDepth($optLevel);
    }

    public function shouldLoadAll(array $categoriesOrIds, $optType)
    {
        $all = Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Optimization::ALL;
        foreach ($categoriesOrIds as $categoryOrId) {
            if ($all === $this->_mmCategoryHelper()->getCategoryOptimization($categoryOrId, $optType)) {
                return true;
            }
        }
        return false;
    }

    protected function _loadAllCategories()
    {
        return $this->_mmCategoryHelper()->loadAllCategories();
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Helper
     */
    protected function _mmCategoryHelper()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_helper');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Optimization
     */
    protected function _mmOptimization()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_attribute_megamenu_source_optimization');
    }

}
