<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 * 
 * @method getImagesData()
 */
abstract class Creatuity_MegaMenu_Block_Widgets_Abstract_Images extends Creatuity_MegaMenu_Block_Widgets_Abstract
{

    public function __construct(array $args = array())
    {
        return parent::__construct($args + array(
                    'is_including_categories_images' => false,
                    'is_including_categories_products_images' => true,
                    'is_linking_enabled' => true,
                    'is_showing_product_names' => true,
                    'is_showing_product_prices' => true,
                    'max_items_count' => $this->_config()->maxWidgetItemsCount(),
                    'image_width' => $this->_config()->widgetSliderImageWidth(),
                    'image_height' => $this->_config()->widgetSliderImageHeight()
        ));
    }

    protected function _getWidgetParams()
    {

        return array_merge(
                parent::_getWidgetParams(), array(
            $this->getData('is_including_categories_images'),
            $this->getData('is_including_categories_products_images'),
            $this->getData('is_linking_enabled'),
            $this->getData('is_showing_product_names'),
            $this->getData('is_showing_product_prices'),
            $this->getData('max_items_count'),
            $this->getData('image_width'),
            $this->getData('image_height'),
                )
        );
    }

    protected function _prepareToRender()
    {
        $imagesData = array();

        $this->_addCategoriesImages($imagesData);
        $this->_addCategoriesProductsImages($imagesData);

        $this->setImagesData($imagesData);
    }

    protected function _addCategoriesImages(array &$imagesData)
    {
        if (!$this->getIsIncludingCategoriesImages()) {
            return;
        }

        foreach ($this->_matchingSubcategories() as $category) {
            if ($this->_isFullOfItems($imagesData)) {
                break;
            }

            $imageUrl = $this->_categoryImageUrl($category);

            if (!$imageUrl) {
                continue;
            }

            $imagesData[] = array(
                'item_type' => 'category-image',
                'src' => $imageUrl,
                'href' => $category->getUrl() ? $category->getUrl() : '#',
            );
        }
    }

    protected function _addCategoriesProductsImages(array &$imagesData)
    {
        if (!$this->getIsIncludingCategoriesProductsImages()) {
            return;
        }

        $noMoreThan = $this->getMaxItemsCount() - count($imagesData);
        foreach ($this->_matchingProducts($noMoreThan) as $product) {
            if ($this->_isFullOfItems($imagesData)) {
                break;
            }

            $imageUrl = $this->_productImageUrl($product);

            if (!$imageUrl) {
                continue;
            }

            $imagesData[] = array(
                'item_type' => 'product-image',
                'src' => $imageUrl,
                'name' => $product->getName(),
                'price' => $this->_productPriceHtml($product),
                'href' => $product->getProductUrl() ? $product->getProductUrl() : '#',
            );
        }
    }

    protected function _isFullOfItems(array &$imagesData)
    {
        return count($imagesData) >= $this->getMaxItemsCount();
    }

    public function getMaxItemsCount()
    {
        if (!$this->hasMaxItemsCount()) {
            $this->setMaxItemsCount($this->_validateNumber('max_items_count'));
        }
        return parent::getMaxItemsCount();
    }

    public function getImageWidth()
    {
        return $this->_validateNumber('image_width');
    }

    public function getImageHeight()
    {
        return $this->_validateNumber('image_height');
    }

    public function getIsIncludingCategoriesImages()
    {
        return $this->_validateBoolean('is_including_categories_images');
    }

    public function getIsIncludingCategoriesProductsImages()
    {
        return $this->_validateBoolean('is_including_categories_products_images');
    }

    public function getIsLinkingEnabled()
    {
        return $this->_validateBoolean('is_linking_enabled');
    }

    public function getIsShowingProductNames()
    {
        return $this->_validateBoolean('is_showing_product_names');
    }

    public function getIsShowingProductPrices()
    {
        return $this->_validateBoolean('is_showing_product_prices');
    }

    public function hasImagesData()
    {
        return (bool) $this->getImagesData();
    }

}
