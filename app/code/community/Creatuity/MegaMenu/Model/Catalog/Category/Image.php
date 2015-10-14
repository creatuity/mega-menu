<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Catalog_Category_Image extends Creatuity_MegaMenu_Model_Catalog_Abstract_Image
{

    public function getThumbnailUrl($categoryOrId, $width, $height, $fieldName = 'thumbnail')
    {
        return $this->_imageUrl($categoryOrId, $width, $height, $fieldName);
    }

    public function getImageUrl($categoryOrId, $width, $height, $fieldName = 'image')
    {
        return $this->_imageUrl($categoryOrId, $width, $height, $fieldName);
    }

    protected function _imageUrl($categoryOrId, $width, $height, $categoryField)
    {
        $category = $this->_mmCategoryHelper()->asCategory($categoryOrId);
        return $this->_entityImageUrl($category, $width, $height, $categoryField);
    }

    protected function _getEntityName()
    {
        return 'category';
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Helper
     */
    protected function _mmCategoryHelper()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_helper');
    }

}
