<?php

class Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Type extends Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Abstract
{

    const MENU = 'menu';
    const MEGAMENU = 'megamenu';
    const LINK = 'link';

    protected $_typesWithContainers = array(
        self::MEGAMENU => 1,
        self::MENU => 1,
    );

    public function getOptionsHash()
    {
        return array(
            self::MENU => 'Menu',
            self::MEGAMENU => 'Mega-Menu',
            self::LINK => 'Link',
        );
    }

    public function getAllTypesWithContainers()
    {
        return array_keys($this->_typesWithContainers);
    }

    public function getAllTypesWithutContainers()
    {
        return array_keys(array_diff_key($this->getOptionsHash(), $this->_typesWithContainers));
    }

    public function hasContainers($type)
    {
        $this->ensureHas($type);
        return isset($this->_typesWithContainers[$type]);
    }

}
