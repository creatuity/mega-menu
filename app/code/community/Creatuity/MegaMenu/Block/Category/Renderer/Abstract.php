<?php

/**
 * @category   Creatuity
 * @package    megamenu
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Block_Category_Renderer_Abstract extends Mage_Core_Block_Template
{

    public function __construct(array $args = array())
    {
        parent::__construct($args + array(
            'category_or_id' => $this->_mmCategoryHelper()->getRootCategoryId(),
        ));
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setCacheLifetime(false);
        $this->addCacheTag($this->_config()->getCacheTags());
    }

    public function getCacheKeyInfo()
    {
        return array_merge(parent::getCacheKeyInfo(), array(
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
            $this->getCategoryId(),
        ));
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return $this->_mmCategoryHelper()->asCategory($this->getCategoryOrId());
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getCategoryId()
    {
        return $this->_mmCategoryHelper()->asCategoryId($this->getCategoryOrId());
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Helper
     */
    protected function _mmCategoryHelper()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_helper');
    }

    /**
     * @return Creatuity_MegaMenu_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('creatuity_megamenu');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Optimization
     */
    protected function _optimization()
    {
        return Mage::getSingleton('creatuity_megamenu/optimization');
    }

    /**
     * 
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getModel('creatuity_megamenu/config');
    }

}
