<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Resource_Setup extends Mage_Catalog_Model_Resource_Eav_Mysql4_Setup
{

    public function addCategoryAttribute($attrCode, array $data)
    {
        $this->_mmCategoryHelper()->ensureIsMegaMenuAttribute($attrCode);

        if (!empty($data['source'])) {
            $model = Mage::getModel($data['source']);
            if (!$model instanceof Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Abstract) {
                $given = is_object($model) ? get_class($model) : var_export($model, true);

                Mage::throwException("Only sources deriven from Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Abstract are spported. Given : {$given}");
            }
        }

        if (@$data['default'] !== null) {
            Mage::throwException("Attribut must have 'null' default value. It means it iherits from parent. ");
        }

        $this->removeAttribute("catalog_category", $attrCode);
        $this->addAttribute("catalog_category", $attrCode, $data);

        return $this;
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Helper
     */
    protected function _mmCategoryHelper()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_helper');
    }

}
