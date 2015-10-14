<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Resource_Catalog_Product_Collection extends Mage_Catalog_Model_Resource_Product_Collection
{

    public function addCategoriesFilter(array $categoriesIds)
    {
        if (!$categoriesIds) {
            $this->_addCategoryFilterId(-1);
            return $this;
        }

        foreach ($categoriesIds as $categoryId) {
            $this->_addCategoryFilterId($categoryId);
        }
        return $this;
    }

    public function addCategoryFilter(Mage_Catalog_Model_Category $category)
    {
        return $this->_addCategoryFilterId($category->getId());
    }

    protected function _addCategoryFilterId($categoryId)
    {
        @$this->_productLimitationFilters['category_ids'][] = $categoryId;
        $this->_productLimitationFilters['category_is_anchor'] = 1;

        if ($this->getStoreId() == Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
            $this->_applyZeroStoreProductLimitations();
        } else {
            $this->_applyProductLimitations();
        }

        return $this;
    }

    protected function _applyProductLimitations()
    {
        $this->_prepareProductLimitationFilters();
        $this->_productLimitationJoinWebsite();
        $this->_productLimitationJoinPrice();
        $filters = $this->_productLimitationFilters;

        if (!isset($filters['category_ids']) && !isset($filters['visibility'])) {
            return $this;
        }

        $conditions = array(
            'cat_index.product_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_index.store_id=?', $filters['store_id'])
        );
        if (isset($filters['visibility']) && !isset($filters['store_table'])) {
            $conditions[] = $this->getConnection()
                    ->quoteInto('cat_index.visibility IN(?)', $filters['visibility']);
        }

        if (!$this->getFlag('disable_root_category_filter')) {
            $conditions[] = $this->getConnection()->quoteInto('cat_index.category_id IN (?)', $filters['category_ids']);
        }

        if (isset($filters['category_is_anchor'])) {
            $conditions[] = $this->getConnection()
                    ->quoteInto('cat_index.is_parent=?', $filters['category_is_anchor']);
        }

        $joinCond = join(' AND ', $conditions);
        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_index'])) {
            $fromPart['cat_index']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        } else {
            $this->getSelect()->join(
                    array('cat_index' => $this->getTable('catalog/category_product_index')), $joinCond, array(
                'cat_index_position' => 'position',
                'category_id' => 'category_id',
                    )
            );
        }

        $this->_productLimitationJoinStore();
        return $this;
    }

    protected function _applyZeroStoreProductLimitations()
    {
        $filters = $this->_productLimitationFilters;

        $conditions = array(
            'cat_pro.product_id=e.entity_id',
            $this->getConnection()->quoteInto('cat_pro.category_id IN (?)', $filters['category_ids'])
        );
        $joinCond = join(' AND ', $conditions);

        $fromPart = $this->getSelect()->getPart(Zend_Db_Select::FROM);
        if (isset($fromPart['cat_pro'])) {
            $fromPart['cat_pro']['joinCondition'] = $joinCond;
            $this->getSelect()->setPart(Zend_Db_Select::FROM, $fromPart);
        } else {
            $this->getSelect()->join(
                    array('cat_pro' => $this->getTable('catalog/category_product')), $joinCond, array('cat_index_position' => 'position')
            );
        }
        $this->_joinFields['position'] = array(
            'table' => 'cat_pro',
            'field' => 'position',
        );

        return $this;
    }

    protected function _prepareProductLimitationFilters()
    {
        if (isset($this->_productLimitationFilters['visibility']) && !isset($this->_productLimitationFilters['store_id'])
        ) {
            $this->_productLimitationFilters['store_id'] = $this->getStoreId();
        }
        if (isset($this->_productLimitationFilters['category_ids']) && !isset($this->_productLimitationFilters['store_id'])
        ) {
            $this->_productLimitationFilters['store_id'] = $this->getStoreId();
        }
        if (isset($this->_productLimitationFilters['store_id']) && isset($this->_productLimitationFilters['visibility']) && !isset($this->_productLimitationFilters['category_ids'])
        ) {
            $this->_productLimitationFilters['category_ids'] = Mage::app()
                    ->getStore($this->_productLimitationFilters['store_id'])
                    ->getRootCategoryId();
        }

        return $this;
    }

}
