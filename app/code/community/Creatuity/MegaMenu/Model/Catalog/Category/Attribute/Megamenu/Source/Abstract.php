<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
abstract class Creatuity_MegaMenu_Model_Catalog_Category_Attribute_Megamenu_Source_Abstract extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }

    public function getAllOptions()
    {
        if ($this->_options === null) {
            foreach ($this->getOptionsHash() as $value => $label) {
                $this->_options[] = array(
                    'label' => Mage::helper('creatuity_megamenu')->__($label),
                    'value' => $value
                );
            }
        }
        return $this->_options;
    }

    public abstract function getOptionsHash();

    public function ensureHas($key)
    {
        if (!$this->has($key)) {
            Mage::throwException("Invalid key '{$key}'");
        }
    }

    public function has($key)
    {
        return array_key_exists($key, $this->getOptionsHash());
    }

}
