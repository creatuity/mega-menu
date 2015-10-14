<?php

/**
 * @category   Creatuity
 * @package    megamenu
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Block_Category_Renderer_Links extends Creatuity_MegaMenu_Block_Category_Renderer_Abstract
{

    public function __construct(array $args = array())
    {
        return parent::__construct($args + array(
                    'template' => 'creatuity/megamenu/links.phtml',
                    'menu_items' => null,
                    'is_view_all' => true,
        ));
    }

    public function getCacheKeyInfo()
    {
        $itemIds = array_keys($this->getMenuItems());
        sort($itemIds);
        return array_merge(
                parent::getCacheKeyInfo(), array(implode('|', $itemIds))
        );
    }

    protected function _beforeToHtml()
    {
        foreach ($this->getMenuItems() as $item) {
            $this->addModelTags($item);
        }
        return parent::_beforeToHtml();
    }

    public function getMenuItems()
    {
        if (parent::getMenuItems() !== null) {
            return (array) parent::getMenuItems();
        }
        return (array) $this->_mmCategoryHelper()->loadCategoryChildren($this->getCategoryOrId());
    }

    public function haveSubmenu(Mage_Catalog_Model_Category $category)
    {
        return $this->_mmCategoryHelper()->hasContainers($category) && $category->getChildrenCount() > 0;
    }

    public function hasThumbnail(Mage_Catalog_Model_Category $category)
    {
        return $this->getCategoryThumbnail($category) !== null;
    }

    public function getCategoryThumbnail(Mage_Catalog_Model_Category $category)
    {
        if (!$category->hasThumbnailUrl()) {
            $category->setThumbnailUrl($this->_mmCategoryHelper()->getThumbnailUrl($category));
        }
        return $category->getThumbnailUrl();
    }

    public function getCategoryThumbnailImgAttributes(Mage_Catalog_Model_Category $category)
    {
        $url = $this->_mmCategoryHelper()->getThumbnailUrl($category);
        $w = $this->_mmCategoryHelper()->getThumbnailWidth($category);
        $h = $this->_mmCategoryHelper()->getThumbnailHeight($category);
        return " src=\"{$url}\" width=\"{$w}px\"  height=\"{$h}px\" ";
    }

    public function getCategoryUrl(Mage_Catalog_Model_Category $category)
    {
        return $category->getUrl();
    }

    public function getIdOfCategory(Mage_Catalog_Model_Category $category)
    {
        return $category->getId();
    }

    public function getCategoryName(Mage_Catalog_Model_Category $category)
    {
        return $category->getName();
    }

    protected function _getCategoryType(Mage_Catalog_Model_Category $category)
    {
        return $this->_mmCategoryHelper()->getCategoryType($category);
    }

    public function getCategoryClassAttribute(Mage_Catalog_Model_Category $category)
    {
        $classes = array();
        if ($this->haveSubmenu($category)) {
            $classes[] = 'parent';
        }
        $frontendClass = $this->_mmCategoryHelper()->getCategoryFrontendClass($category);
        if (strlen($frontendClass) > 0) {
            $classes[] = $frontendClass;
        }
        return count($classes) ? 'class="' . implode(' ', $classes) . '"' : '';
    }

}
