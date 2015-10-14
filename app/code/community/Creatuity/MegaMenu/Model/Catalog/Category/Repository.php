<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Catalog_Category_Repository
{

    /**
     * @var Mage_Catalog_Model_Category[]
     */
    protected $_categoriesCache = array();
    protected $_debugDbLoads = false;
    protected $_childrenCategoriesCache = array();
    protected $_requiredCategoryFields = array(
        'name',
        'thumbnail',
        'image',
    );

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function asCategory($categoryOrId, $ensureExists = true)
    {
        if (is_numeric($categoryOrId)) {
            $cat = $this->loadCategoryModel($categoryOrId);
            if ($ensureExists && !$cat->getId()) {
                Mage::throwException('No category with id "' . $categoryOrId . '"');
            }
            return $cat;
        } elseif ($categoryOrId instanceof Mage_Catalog_Model_Category) {
            return $categoryOrId;
        }
        Mage::throwException('Unknown parameter type');
    }

    public function asCategoryId($categoryOrId)
    {
        if (is_numeric($categoryOrId)) {
            return (int) $categoryOrId;
        } elseif ($categoryOrId instanceof Mage_Catalog_Model_Category) {
            return $categoryOrId->getId();
        }
        Mage::throwException('Unknown parameter type');
    }

    public function loadCategoryModel($categoryId)
    {
        return $this->_categoryModel($categoryId);
    }

    public function loadCategoryChildren($parentCategoryOrId, $level = 1, array $additionalFields = array())
    {
        return $this->loadCategoriesChildren(array($parentCategoryOrId), array($level), true, $additionalFields);
    }

    public function loadCategoriesChildren(array $parentCategoriesOrIds, array $levels, $isFlatResult = false, array $additionalFields = array())
    {
        if (count($parentCategoriesOrIds) != count($levels)) {
            Mage::throwException('Invalid usage');
        }

        $results = array();

        $paramsCacheKey = implode('|', $additionalFields);



        // Determine SQL conditions
        $orConditions = array();
        foreach ($parentCategoriesOrIds as $i => $catOrId) {
            $parentId = $this->asCategoryId($catOrId);
            $level = $levels[$i];

            if (isset($this->_childrenCategoriesCache[$parentId . '_' . $level . '_' . $paramsCacheKey])) {
                $itemsFromCache = $this->_childrenCategoriesCache[$parentId . '_' . $level . '_' . $paramsCacheKey];
                $this->_addToResults($results, $parentId, $itemsFromCache, $isFlatResult);
                continue;
            }

            $this->_addToConditions($orConditions, $level, $parentId, $catOrId);
        }

        // Load items from db
        $items = array();
        if ($orConditions) {
            $collection = Mage::getResourceModel('catalog/category_collection');
            /* @var $collection Mage_Catalog_Model_Resource_Category_Collection */
            $collection->getSelect()->where('((' . implode(') OR (', $orConditions) . '))');
            $collection->addIsActiveFilter();
            $collection->addAttributeToFilter('include_in_menu', 1);
            $items = $this->loadCollection($collection->setOrder('position', 'asc'), $additionalFields);
        }

        // assign results to propper parents
        foreach ($parentCategoriesOrIds as $i => $catOrId) {
            $parentId = $this->asCategoryId($catOrId);
            $level = $levels[$i];

            foreach ($items as $category) {
                $isChild = $level === 1 ? $category->getParentId() == $this->asCategoryId($catOrId) : 0 === strpos($category->getPath(), $this->asCategory($catOrId)->getPath());

                if ($isChild) {
                    @$this->_childrenCategoriesCache[$parentId . '_' . $level . '_' . $paramsCacheKey][$category->getId()] = $category;
                    $this->_addToResults($results, $parentId, $category, $isFlatResult);
                }
            }
        }

        return $results;
    }

    protected function _addToConditions(&$orConditions, $level, $parentId, $catOrId)
    {
        if ($level < 0) {
            Mage::throwException("Negative Level ?");
            return;
        }
        switch ($level) {
            case 1:
                $orConditions[] = "parent_id = '$parentId'";
                break;
            case PHP_INT_MAX:
                $category = $this->asCategory($catOrId);
                $pathPrefix = $category->getPath() . '/';
                $orConditions[] = "path LIKE '{$pathPrefix}%'";
                break;
            default:
                $category = $this->asCategory($catOrId);
                $pathPrefix = $category->getPath() . '/';
                $l = $category->getLevel() + $level + 1;

                $orConditions[] = "(path LIKE '{$pathPrefix}%') AND ((LENGTH(path) - LENGTH(REPLACE(path, '/', ''))) < {$l})";
        }
    }

    protected function _addToResults(&$results, $parentCategoryId, $categories, $flatResult)
    {
        if (!is_array($categories)) {
            $categories = array($categories->getId() => $categories);
        }

        if ($flatResult) {
            $results += $categories;
        } else {
            if (!isset($results[$parentCategoryId])) {
                $results[$parentCategoryId] = array();
            }
            $results[$parentCategoryId] += $categories;
        }
    }

    public function loadCollection(Mage_Catalog_Model_Resource_Category_Collection $collection, array $additionalFields = array())
    {
        $toLoad = array_unique(array_merge(
                        $this->_requiredCategoryFields, $this->_config()->megaMenuAttributeCodes(), $additionalFields
        ));

        foreach ($toLoad as $field) {
            $collection->addAttributeToSelect($field);
        }

        $childreen = $collection->load($this->_debugDbLoads)->getItems();

        if ($this->_debugDbLoads) {
            foreach ($childreen as $child) {
                Mage::log('collection item loaded' . var_export($child->debug(), true));
            }
        }

        $this->_categoriesCache = $childreen + $this->_categoriesCache;
        return $childreen;
    }

    protected function _categoryModel($categoryId)
    {
        if (!isset($this->_categoriesCache[$categoryId])) {
            $this->_categoriesCache[$categoryId] = Mage::getModel('catalog/category')->load($categoryId);
            if ($this->_debugDbLoads) {
                Mage::log("model loaded" . var_export($this->_categoriesCache[$categoryId]->debug(), true));
            }
        }
        return $this->_categoriesCache[$categoryId];
    }

    /**
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getSingleton('creatuity_megamenu/config');
    }

}
