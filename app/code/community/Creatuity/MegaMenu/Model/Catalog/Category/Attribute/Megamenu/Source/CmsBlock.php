<?php

class Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_CmsBlock extends Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Abstract
{

    const NONE = -1;

    public function getOptionsHash()
    {
        $ret = array();
        foreach (Mage::getResourceModel('cms/block_collection') as $block) {
            $ret[$block->getId()] = $block->getTitle();
        }
        return $ret;
    }

}
