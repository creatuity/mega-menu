<?php

class Creatuity_MegaMenu_Block_Topmenu extends Mage_Core_Block_Template
{

    protected function _construct()
    {
        parent::_construct();
        $this->setCacheLifetime(false);
        $this->addCacheTag($this->_config()->getCacheTags());
    }

    public function getCacheKeyInfo()
    {
        return array_merge(parent::getCacheKeyInfo(), array(
            Mage::getDesign()->getPackageName(),
            Mage::getDesign()->getTheme('template'),
            Mage::getSingleton('customer/session')->getCustomerGroupId(),
        ));
    }

    public function getHtml($outermostClass = '', $childrenWrapClass = '')
    {

        $html = '';
        if ($renderer = $this->getChild('catalog.topnav.renderer')) {
            $renderer->setChildrenWrapClass($childrenWrapClass);
            $html = $renderer->toHtml();
        }
        return $html;
    }

    /**
     * 
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getModel('creatuity_megamenu/config');
    }

}
