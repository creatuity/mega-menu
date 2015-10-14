<?php

/**
 * 
 * @method getIsValid()
 * @method getLinksHtml()
 * @method getInvalidStateMessage()
 */
class Creatuity_MegaMenu_Block_Widgets_Links extends Creatuity_MegaMenu_Block_Widgets_Abstract implements Mage_Widget_Block_Interface
{

    public function __construct(array $args = array())
    {
        return parent::__construct($args + array(
                    'template' => 'creatuity/megamenu/widget-links.phtml',
        ));
    }

    protected function _prepareToRender()
    {
        $this->setLinksHtml($this->getLayout()->createBlock('creatuity_megamenu/category_renderer_links', '', array(
                    'menu_items' => $this->_matchingSubcategories(),
                    'is_view_all' => false,
                ))->toHtml());
    }

}
