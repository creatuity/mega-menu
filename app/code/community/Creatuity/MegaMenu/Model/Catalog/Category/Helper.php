<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Catalog_Category_Helper
{

    protected $_rendererBlock;
    protected $_megaMenuGrId;

    public function getRootCategoryId()
    {
        return Mage::app()->getStore()->getRootCategoryId();
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function asCategory($categoryOrId, $ensureExists = true)
    {
        return $this->_repository()->asCategory($categoryOrId, $ensureExists);
    }

    public function asCategoryId($categoryOrId)
    {
        return $this->_repository()->asCategoryId($categoryOrId);
    }

    public function getCategoryType($categoryOrId)
    {
        return $this->_mmCatalogCategoryAttribute()->getCategoryAttrValue($categoryOrId, 'megamenu_type');
    }

    public function getCategoryCmsBlockId($categoryOrId)
    {
        return $this->_mmCatalogCategoryAttribute()->getCategoryAttrValue($categoryOrId, 'megamenu_cms_block');
    }

    public function getCategoryOptimization($categoryOrId, $type)
    {
        $code = 'megamenu_optlevel_' . $type;
        $this->ensureIsMegaMenuAttribute($code);
        return $this->_mmCatalogCategoryAttribute()->getCategoryAttrValue($categoryOrId, $code);
    }

    public function getCategoryFrontendClass($categoryOrId)
    {
        return $this->_mmCatalogCategoryAttribute()->getCategoryAttrValue($categoryOrId, 'megamenu_frontend_class');
    }

    public function hasContainers($categoryOrId)
    {
        return $this->_mmTypes()->hasContainers($this->getCategoryType($categoryOrId));
    }

    public function getThumbnailUrl($categoryOrId)
    {
        return $this->_image()->getThumbnailUrl($categoryOrId, $this->getThumbnailWidth(), $this->getThumbnailHeight());
    }

    public function getThumbnailWidth()
    {
        return $this->_config()->thumbnailWidth();
    }

    public function getThumbnailHeight()
    {
        return $this->_config()->thumbnailWidth();
    }

    public function loadCategoryChildren($parentCategoryOrId, $level = 1, array $additionalFields = array())
    {
        return $this->_repository()->loadCategoryChildren($parentCategoryOrId, $level, $additionalFields);
    }

    public function loadAllCategories()
    {
        return $this->loadCategoriesChildren(array($this->getRootCategoryId()), array(PHP_INT_MAX), true);
    }

    public function loadCategoriesChildren(array $parentCategoriesOrIds, array $levels, $isFlatResult = false)
    {
        return $this->_repository()->loadCategoriesChildren($parentCategoriesOrIds, $levels, $isFlatResult);
    }

    public function loadCollection(Mage_Catalog_Model_Resource_Category_Collection $collection)
    {
        return $this->_repository()->loadCollection($collection);
    }

    public function renderContainerHtml($categoryOrId)
    {
        return $this->_categoryRenderer($categoryOrId)->setCategoryOrId($categoryOrId)->toHtml();
    }

    public function ensureIsMegaMenuAttribute($attrCode, $checkDefaultValue = true)
    {
        if (!$this->_config()->isMegaMenuAttribute($attrCode)) {
            Mage::throwException("Please add {$attrCode} to attributes list, first.");
        }

        if ($checkDefaultValue && $this->_config()->defaultAttributeValue($attrCode) === null) {
            Mage::throwException("Please add default value for {$attrCode}.");
        }
    }

    /**
     * @return Creatuity_MegaMenu_Block_Category_Renderer
     */
    protected function _categoryRenderer()
    {
        if ($this->_rendererBlock === null) {
            $this->_rendererBlock = Mage::app()->getLayout()->createBlock('creatuity_megamenu/category_renderer');
        }
        return $this->_rendererBlock;
    }

    public function getMegaMenuGroupId()
    {
        if ($this->_megaMenuGrId === null) {
            /** @var $groupCollection Mage_Eav_Model_Resource_Entity_Attribute_Group_Collection */
            $groupCollection = Mage::getResourceModel('eav/entity_attribute_group_collection')
                    ->addFieldToSelect('attribute_group_id')
                    ->setAttributeSetFilter(Mage::getModel('catalog/category')->getDefaultAttributeSetId())
                    ->addFieldToFilter('attribute_group_name', Creatuity_MegaMenu_Model_Config::MEGA_MENU_TAB_NAME);
            $this->_megaMenuGrId = $groupCollection->getFirstItem()->getId();
        }
        return $this->_megaMenuGrId;
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Type        
     */
    protected function _mmTypes()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_attribute_megamenu_source_type');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Attribute
     */
    protected function _mmCatalogCategoryAttribute()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_attribute');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Image
     */
    protected function _image()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_image');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Repository
     */
    protected function _repository()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_repository');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getSingleton('creatuity_megamenu/config');
    }

}
