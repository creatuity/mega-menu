<?php

class Creatuity_MegaMenu_Block_Widgets_Slider extends Creatuity_MegaMenu_Block_Widgets_Abstract_Images
{

    public function __construct(array $args = array())
    {
        return parent::__construct($args + array(
                    'template' => 'creatuity/megamenu/widget-slider.phtml',
        ));
    }

}
