<?php

class Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Optimization extends Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Abstract
{

    const NONE = 'none';
    const CHILDS_LVL1 = 'childs_lvl1';
    const CHILDS_LVL2 = 'childs_lvl2';
    const CHILDS_LVL3 = 'childs_lvl3';
    const CHILDS_ALL = 'childs_all';
    const ALL = 'all';

    public function getOptionsHash()
    {
        return array(
            self::NONE => 'None',
            self::CHILDS_LVL1 => '1-st Level Childs',
            self::CHILDS_LVL2 => '2-nd Level Childs',
            self::CHILDS_LVL3 => '3-rd Level Childs',
            self::CHILDS_ALL => 'All Childs',
            self::ALL => 'All',
        );
    }

    public function toChildrenLevelDepth($lvl)
    {
        $this->ensureHas($lvl);

        switch ($lvl) {
            case self::NONE:
                return 0;
            case self::CHILDS_LVL1:
                return 1;
            case self::CHILDS_LVL2:
                return 2;
            case self::CHILDS_LVL3:
                return 3;
            case self::CHILDS_ALL;
                return PHP_INT_MAX;
            case self::ALL:
                Mage::throwException('It is not a children depth flag');
        }
    }

}
