<?php

/**
 * 
 * @category   Creatuity
 * @package    megamenu
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Block_Category_Renderer_Root extends Creatuity_MegaMenu_Block_Category_Renderer_Abstract
{

    protected $_categoriesCache;

    public function __construct(array $args = array())
    {
        return parent::__construct($args + array(
                    'template' => 'creatuity/megamenu/topnav.phtml',
        ));
    }

    protected function _beforeToHtml()
    {
        if ($this->_categoriesCache !== null) {
            foreach ($this->_categoriesCache as $category) {
                $this->addModelTags($category);
            }
        }
        return parent::_beforeToHtml();
    }

    public function getLinksHtml()
    {
        return $this->_linksRenderer()->toHtml();
    }

    public function getMenusHtml()
    {
        return $this->_getCategoriesHtmlOfType(
                        Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Type::MENU);
    }

    public function getMegamenusHtml()
    {
        return $this->_getCategoriesHtmlOfType(
                        Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Type::MEGAMENU);
    }

    protected function _getCategoriesHtmlOfType($type)
    {
        $menusHtml = '';
        foreach ($this->_categoriesUsedInEveryRequest() as $category) {
            if ($this->_mmCategoryHelper()->getCategoryType($category) === $type) {
                $menusHtml .= $this->_mmCategoryHelper()->renderContainerHtml($category);
            }
        }
        return $menusHtml;
    }

    protected function _categoriesUsedInEveryRequest()
    {
        if ($this->_categoriesCache === null) {
            $topNavCategories = $this->_mmCategoryHelper()->loadCategoryChildren($this->getCategoryOrId());
            $this->_categoriesCache = $this->_optimization()->loadRelatedCategoriesForRootCategories(array_keys($topNavCategories));
        }
        return $this->_categoriesCache;
    }

    /**
     * @return Creatuity_MegaMenu_Block_Category_Renderer_Links
     */
    protected function _linksRenderer()
    {
        return $this->getLayout()->createBlock('creatuity_megamenu/category_renderer_links', '', array(
                    'category_or_id' => $this->getCategoryOrId(),
                    'is_view_all' => false,
        ));
    }

}
