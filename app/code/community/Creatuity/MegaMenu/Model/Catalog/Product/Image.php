<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Catalog_Product_Image extends Creatuity_MegaMenu_Model_Catalog_Abstract_Image
{

    public function getImageUrl(Mage_Catalog_Model_Product $product, $width, $height, $fieldName = 'small_image')
    {
        return $this->_entityImageUrl($product, $width, $height, $fieldName);
    }

    protected function _getEntityName()
    {
        return 'product';
    }

}
