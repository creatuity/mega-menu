<?php

class Creatuity_MegaMenu_MegamenuController extends Mage_Core_Controller_Front_Action
{

    public function indexAction()
    {
        $this->_ajax()->buildAndSendResponse($this->getRequest(), $this->getResponse());
    }

    /**
     * @return Creatuity_MegaMenu_Model_Ajax
     */
    protected function _ajax()
    {
        return Mage::getModel('creatuity_megamenu/ajax');
    }

}
