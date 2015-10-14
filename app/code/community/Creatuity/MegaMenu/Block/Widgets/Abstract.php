<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class ValidateException extends Exception
{
    
}

abstract class Creatuity_MegaMenu_Block_Widgets_Abstract extends Creatuity_MegaMenu_Block_Category_Renderer_Abstract implements Mage_Widget_Block_Interface
{

    public function __construct(array $args = array())
    {
        return parent::__construct($args + array(
                    'is_valid' => false,
                    'invalid_state_message' => "Widget has been improperly configured",
                    'category_id' => false,
                    'product_image_field' => 'small_image',
                    'category_image_field' => 'image',
                    'subcategories_level' => 1,
                    'products_order_random_seed' => 1234,
                    'store_id' => Mage::app()->getStore()->getId(),
        ));
    }

    public function getCacheKeyInfo()
    {
        return array_merge(
                parent::getCacheKeyInfo(), $this->_getWidgetParams(), array($this->getCategoryId())
        );
    }

    protected function _getWidgetParams()
    {
        return array(
            $this->getData('scope_type'),
            $this->getData('from_numeric'),
            $this->getData('to_numeric'),
            $this->getData('from_percent'),
            $this->getData('to_percent'),
            $this->getData('is_using_advanced_parameters'),
            $this->getData('category_id'),
            $this->getData('product_image_field'),
            $this->getData('category_image_field'),
            $this->getData('subcategories_level'),
            $this->getData('products_order_random_seed'),
        );
    }

    protected function _beforeToHtml()
    {
        try {
            $this->_prepareToRender();
            $this->setIsValid(true);
        } catch (Exception $e) {
            $this->setIsValid(false);
            $this->setInvalidStateMessage($e->getMessage());

            if (Mage::getIsDeveloperMode()) {
                Mage::logException($e);
            } else {
                throw $e;
            }
        }
    }

    abstract protected function _prepareToRender();

    /**
     * @return Mage_Catalog_Model_Category[]
     */
    protected function _matchingSubcategories()
    {
        if ($this->getData('scope_type') === 'numeric') {
            return $this->_subsetOfCategoriesNumeric();
        } elseif ($this->getData('scope_type') === 'percent') {
            return $this->_subsetOfCategoriesPercent();
        }
        Mage::throwException('Parameter "scope_type" must be one of values: "numeric", "percent" ');
    }

    /**
     * @return Mage_Catalog_Model_Category[]
     */
    protected function _allSubcategories()
    {
        return $this->_mmCategoryHelper()->loadCategoryChildren($this->getCategoryOrId(), $this->getSubcategoriesLevel(), array(
                    $this->getCategoryImageField()
        ));
    }

    public function getSubcategoriesLevel()
    {
        return $this->_validateNumber('subcategories_level');
    }

    protected function _subsetOfCategoriesNumeric()
    {
        $from = $this->_validateNumber('from_numeric');
        $to = $this->_validateNumber('to_numeric');

        $items = $this->_allSubcategories();

        if ($from > $to) {
            Mage::throwException("'from_numeric' must be less or equal to 'to_numeric' ");
        }

        return array_slice($items, $from - 1, $to - $from + 1, true);
    }

    protected function _subsetOfCategoriesPercent()
    {
        $fromPercent = $this->_validatePercent('from_percent');
        $toPercent = $this->_validatePercent('to_percent');

        $items = $this->_allSubcategories();

        if (!$items) {
            return array();
        }

        $from = ceil((count($items) - 1) * $fromPercent);
        $to = floor((count($items) - 1) * $toPercent);


        if ($from > $to) {
            Mage::throwException("'from_percent' must be less or equal to 'to_percent'. ");
        }

        return array_slice($items, $from, $to - $from + 1, true);
    }

    /**
     * @return Mage_Catalog_Model_Product[]
     */
    protected function _matchingProducts($limit = null)
    {
        if ($limit === 0) {
            return array();
        }

        $collection = $this->_newProductsCollection()
                ->setStoreId($this->getStoreId())
                ->setPageSize($limit)
                ->applyFrontendPriceLimitations()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect($this->getProductImageField())
                ->addCategoriesFilter(array_keys($this->_matchingSubcategories()));

        $collection->getSelect()->order("RAND({$this->getProductsOrderRandomSeed()})");
        $collection->getSelect()->group("e.entity_id");

        return $collection->load()->getItems();
    }

    public function getStoreId()
    {
        return $this->_validateNumber('store_id');
    }

    public function getProductsOrderRandomSeed()
    {
        return $this->_validateNumber('products_order_random_seed');
    }

    public function getCategoryOrId()
    {
        if ($this->getData('category_id') > 0) {
            return $this->_validateNumber($this->getData('category_id'));
        }

        if (Mage::registry('current_megamenu_category_or_id')) {
            return Mage::registry('current_megamenu_category_or_id');
        }

        if ($this->getData('category_or_id')) {
            return $this->getData('category_or_id');
        }

        Mage::throwException('Neither "current_megamenu_category_or_id" is in the registry.'
                . ' Neither "category_id" widget parameter has been provided.'
                . ' Neither "category_or_id" block parameter has been provided.');
    }

    protected function _categoryImageUrl(Mage_Catalog_Model_Category $category)
    {
        return $this->_categoryImages()->getImageUrl($category, $this->getImageWidth(), $this->getImageHeight(), $this->getCategoryImageField()
        );
    }

    protected function _productImageUrl(Mage_Catalog_Model_Product $product)
    {
        return $this->_productImages()->getImageUrl($product, $this->getImageWidth(), $this->getImageHeight(), $this->getProductImageField());
    }

    protected function _productPriceHtml(Mage_Catalog_Model_Product $product)
    {
        if (!$this->hasBlockPrice()) {
            $block = $this->getLayout()->createBlock('catalog/product_price');
            $block->setTemplate('catalog/product/price.phtml');
            $this->setBlockPrice($block);
        }
        return $this->getBlockPrice()->setProduct($product)->toHtml();
    }

    protected function _validatePercent($paramName)
    {
        $v = $this->_validateNumber($paramName, false);
        if (!($v >= 0 && $v <= 100)) {
            Mage::throwException("\"{$paramName}\" parameter must in range 0-100.");
        }
        return (float) $v / 100.0;
    }

    protected function _validateNumber($paramName, $grZero = true, $defaultVal = 0)
    {
        $v = $this->getData($paramName);
        $intV = (int) $v;

        if ($v === null) {
            return (int) $defaultVal;
        }

        if ($intV === -1 || $v === 'infinity') {
            return PHP_INT_MAX;
        }

        if ((string) $intV !== (string) $v) {
            Mage::throwException("\"{$paramName}\" parameter must be an integer number.");
        }

        if ($intV < -1) {
            Mage::throwException("\"{$paramName}\" parameter cannot be lower than -1.");
        }
        if ($grZero && $intV === 0) {
            Mage::throwException("\"{$paramName}\" parameter cannot be zero.");
        }
        return $intV;
    }

    protected function _validateBoolean($paramName, $defaultVal = false)
    {
        $v = $this->getData($paramName);

        if ($v === null) {
            return (bool) $defaultVal;
        }

        if ($v === 'true' || $v === '1' || $v === 'yes' || $v === true) {
            return true;
        }

        if ($v === 'false' || $v === '0' || $v === 'no' || $v === false) {
            return false;
        }

        Mage::throwException("Cannot interprate '{$paramName}' as boolean value.");
    }

    /**
     * @return Creatuity_MegaMenu_Model_Resource_Catalog_Product_Collection
     */
    protected function _newProductsCollection()
    {
        return Mage::getResourceModel('creatuity_megamenu/catalog_product_collection');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Image
     */
    protected function _categoryImages()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_image');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Product_Image
     */
    protected function _productImages()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_product_image');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getSingleton('creatuity_megamenu/config');
    }

}
