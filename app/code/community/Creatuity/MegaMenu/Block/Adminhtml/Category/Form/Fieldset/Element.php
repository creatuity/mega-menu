<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Block_Adminhtml_Category_Form_Fieldset_Element extends Mage_Adminhtml_Block_Catalog_Form_Renderer_Fieldset_Element
{

    protected $_usedDefault;

    protected function _construct()
    {
        parent::_construct();

        $this->setTemplate('creatuity/megamenu/form/element.phtml');
    }

    protected function _toHtml()
    {
        $this->_usedDefault = !$this->getElement()->getValue();
        if ($this->_usedDefault) {
            $this->getElement()->setValue($this->getValueOfParentCategory());
        }
        return parent::_toHtml();
    }

    public function canDisplayUseDefault()
    {
        return true;
    }

    public function getElementHtmlId()
    {
        return $this->getElement()->getHtmlId();
    }

    public function usedDefault()
    {
        return $this->_usedDefault;
    }

    public function getValueOfParentCategory()
    {
        return $this->_mmCatalogCategoryAttribute()->getParentCategoryAttrValue($this->getCategory(), $this->getElement()->getId());
    }

    public function getIsTopCategory()
    {
        return $this->getCategory()->getLevel() == 1;
    }

    /**
     * @return Mage_Catalog_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Attribute
     */
    protected function _mmCatalogCategoryAttribute()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_attribute');
    }

}
