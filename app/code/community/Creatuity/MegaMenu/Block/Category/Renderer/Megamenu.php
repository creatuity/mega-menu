<?php

/**
 * @category   Creatuity
 * @package    megamenu
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Block_Category_Renderer_Megamenu extends Creatuity_MegaMenu_Block_Category_Renderer_Abstract
{

    public function __construct(array $args = array())
    {
        return parent::__construct($args + array(
                    'template' => 'creatuity/megamenu/megamenu.phtml'
        ));
    }

    protected function _toHtml()
    {
        Mage::register('current_megamenu_category_or_id', $this->getCategoryOrId());
        $html = parent::_toHtml();
        Mage::unregister('current_megamenu_category_or_id');
        return $html;
    }

    public function getCmsBlockHtml()
    {
        $blockId = $this->_mmCategoryHelper()->getCategoryCmsBlockId($this->getCategoryOrId());
        $block = $this->getLayout()->createBlock('cms/block')->setBlockId($blockId);
        return $block->toHtml();
    }

}
