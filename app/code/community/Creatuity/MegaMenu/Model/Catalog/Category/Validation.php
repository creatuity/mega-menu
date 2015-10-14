<?php

class Creatuity_MegaMenu_Model_Catalog_Category_Validation
{

    protected $_category;
    protected $_block;
    protected $_warnings = array();
    protected $_stores;
    protected $_currentStore;

    public function validate(Mage_Catalog_Model_Category $category)
    {
        try {
            $this->_prepareData($category);
            if (!$this->_block->getIsActive()) {
                $this->_addWarning("Selected MegaMenu Block is disabled!");
            }

            if ($this->_currentStore != 0) {
                $this->_currentStoreValidation();
            } elseif (!$this->_isBlockGloballyAvailable()) {
                $this->_allStoresValidation();
            }

            if (count($this->_warnings)) {
                $this->_flushWarnings();
            }
        } catch (Mage_Exception $e) {
            $msg = Mage::helper('creatuity_megamenu')->__("Something went wrong with validation.");
            Mage::getSingleton('adminhtml/session')->addNotice($msg);
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    protected function _prepareData(Mage_Catalog_Model_Category $category)
    {
        $this->_category = $category;
        $this->_currentStore = $category->getStoreId();
        $this->_block = Mage::getModel('cms/block')->load($category->getMegamenuCmsBlock());
    }

    protected function _addWarning($msg)
    {
        $this->_warnings[] = $msg;
    }

    protected function _isBlockGloballyAvailable()
    {
        return in_array(0, $this->_block->getStores());
    }

    protected function _currentStoreValidation()
    {
        if (!in_array($this->_currentStore, $this->_block->getStores())) {
            $this->_addWarning("Selected MegaMenu CMS Block is not available in current store view. MegaMenu will not be rendered for saved category.");
        }
    }

    protected function _allStoresValidation()
    {
        $storesWhereBlockIsNotVisible = array_diff($this->_getActiveStoreViews(), $this->_block->getStores());
        foreach ($storesWhereBlockIsNotVisible as $storeId) {
            if ($storeId == 0) {
                continue;
            }
            if ($this->_isCategoryUseTheSameBlockInStoreView($storeId)) {
                $this->_addWarning("Selected MegaMenu Block is disabled for storeId: " . $storeId);
            }
        }
    }

    protected function _isCategoryUseTheSameBlockInStoreView($storeId)
    {
        $model = Mage::getModel('catalog/category');
        $model->setStoreId($storeId);
        $category = $model->load($this->_category->getId());

        if ($category->getMegamenuCmsBlock() == $this->_category->getMegamenuCmsBlock()) {
            return true;
        } else {
            return false;
        }
    }

    protected function _getActiveStoreViews()
    {
        if (!$this->_stores) {
            $read = Mage::getSingleton('core/resource')->getConnection('core_read');
            $select = $read->select('store_id')
                    ->from('core_store', 'store_id')
                    ->where('is_active', 1);
            $this->_stores = $read->fetchCol($select);
        }
        return $this->_stores;
    }

    protected function _flushWarnings()
    {
        $msg = Mage::helper('creatuity_megamenu')->__("WARNING! Incorrect MegaMenu configuration:");
        foreach ($this->_warnings as $warning) {
            $msg .= "<br />";
            $msg .= Mage::helper('creatuity_megamenu')->__($warning);
        }
        Mage::getSingleton('adminhtml/session')->addNotice($msg);
    }

}
