<?php

/**
 * @category   Creatuity
 * @package    megamenu
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Block_Category_Renderer_Menu extends Creatuity_MegaMenu_Block_Category_Renderer_Abstract
{

    public function __construct(array $args = array())
    {
        return parent::__construct($args + array(
                    'template' => 'creatuity/megamenu/menu.phtml',
        ));
    }

    public function getLinksHtml()
    {
        return $this->getLayout()->createBlock('creatuity_megamenu/category_renderer_links', '', array(
                    'category_or_id' => $this->getCategoryOrId()
                ))->toHtml();
    }

}
