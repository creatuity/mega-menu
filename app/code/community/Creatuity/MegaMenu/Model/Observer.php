<?php

class Creatuity_MegaMenu_Model_Observer
{

    public function onAdminhtmlCatalogCategoryTabs(Varien_Event_Observer $observer)
    {
        if (!$this->_config()->isModuleEnabled()) {
            $observer->getEvent()->getTabs()->removeTab('group_' . $this->_mmCategoryHelper()->getMegaMenuGroupId());
        }
    }

    public function onAdminCatalogCategoryEditPrepareForm(Varien_Event_Observer $observer)
    {
        $this->_overrideMegamenuFieldset($observer->getEvent()->getForm());
    }

    protected function _overrideMegamenuFieldset(Varien_Data_Form $form)
    {
        $fieldset = $form->getElement('fieldset_group_' . $this->_mmCategoryHelper()->getMegaMenuGroupId());
        if (!$fieldset) {
            return;
        }

        /* @var $fieldset Varien_Data_Form_Element_Fieldset */
        foreach ($fieldset->getSortedElements() as $element) {
            $element->setRenderer(
                    Mage::app()->getLayout()->createBlock('creatuity_megamenu/adminhtml_category_form_fieldset_element'));
        }
    }

    public function onCatalogCategoryPrepareSave(Varien_Event_Observer $observer)
    {
        $category = $observer->getEvent()->getCategory();
        $this->_mmCategoryValidator()->validate($category);
    }

    /**
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getSingleton('creatuity_megamenu/config');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Helper
     */
    protected function _mmCategoryHelper()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_helper');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Validation
     */
    protected function _mmCategoryValidator()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_validation');
    }

}
