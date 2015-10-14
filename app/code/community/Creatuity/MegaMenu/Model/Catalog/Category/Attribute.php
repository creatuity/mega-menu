<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Catalog_Category_Attribute
{

    public function getCategoryAttrValue($categoryOrId, $attrCode)
    {
        return $this->_calcCategoryAttrValue($categoryOrId, $attrCode, 0);
    }

    public function getParentCategoryAttrValue($categoryOrId, $attrCode)
    {
        return $this->_calcCategoryAttrValue($categoryOrId, $attrCode, 1);
    }

    protected function _calcCategoryAttrValue($categoryOrId, $attrCode, $lvlUp = 0)
    {
        $category = $this->_mmCategoryHelper()->asCategory($categoryOrId);

        $value = (string) $category->getData($attrCode);

        $infLoopProtector = 1000;
        while ($value === '' || $lvlUp > 0) {
            if (--$infLoopProtector == 0) {
                Mage::throwException('Bad programmer!');
            }
            if ($category->getLevel() > 1) {
                $category = $this->_mmCategoryHelper()->asCategory($category->getParentId());
                $lvlUp--;
                $value = (string) $category->getData($attrCode);
            } else {
                $value = '';
                break;
            }
        }

        return $value === '' ? $this->_config()->defaultAttributeValue($attrCode) : $value;
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Helper
     */
    protected function _mmCategoryHelper()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_helper');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getSingleton('creatuity_megamenu/config');
    }

}
