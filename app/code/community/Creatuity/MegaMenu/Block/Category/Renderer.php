<?php

/**
 * @category   Creatuity
 * @package    megamenu
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 * 
 * @method setCategoryOrId($cat)
 */
class Creatuity_MegaMenu_Block_Category_Renderer extends Creatuity_MegaMenu_Block_Category_Renderer_Abstract
{

    protected $_renderers = array();

    protected function _toHtml()
    {
        $type = $this->_mmCategoryHelper()->getCategoryType($this->getCategoryOrId());

        if (!$this->_mmCategoryHelper()->hasContainers($this->getCategoryOrId())) {
            return '';
        }

        if (!isset($this->_renderers[$type])) {
            $this->_renderers[$type] = $this->getLayout()->createBlock('creatuity_megamenu/category_renderer_' . $type);
        }
        if (!$this->_renderers[$type]) {
            Mage::throwException('Cannot load renderer for category type "' . $type . '"');
        }

        return $this->_renderers[$type]->addData($this->getData())->toHtml();
    }

}
