<?php

/**
 * @category   Creatuity
 * @package    megamenu
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Ajax_UserException extends Exception
{
    
}

class Creatuity_MegaMenu_Model_Ajax
{

    protected $_jsonResponse;
    protected $_ajaxResponseTemplate = array(
        'status' => false,
        'error_message' => null,
        'results' => array(),
    );

    public function buildAndSendResponse(Mage_Core_Controller_Request_Http $httpRequest, Mage_Core_Controller_Response_Http $httpResponse)
    {
        try {
            $categoryId = $httpRequest->getParam('category_id');
            $allMode = $httpRequest->getParam('all_categories_mode');
            if (!$allMode && !$categoryId) {
                throw new Creatuity_MegaMenu_Model_Ajax_UserException("'category_id' parameter is missing");
            }

            $cacheKey = $allMode ? $categoryId : 0;
            $this->_jsonResponse = $this->_cache()->loadCache($cacheKey);
            if ($this->_jsonResponse === false) {
                $this->_jsonResponse = $this->_process($categoryId, $allMode);
                $this->_cache()->saveCache($cacheKey, $this->_jsonResponse);
            }

            $this->_renderResponse($httpResponse, $this->_jsonResponse, 200);
            $this->_cache()->setNeverExpireBrowserCacheHeader($httpRequest, $httpResponse);
        } catch (Creatuity_MegaMenu_Model_Ajax_UserException $e) {
            $this->_renderResponse($httpResponse, $this->_errorResponse($e->getMessage()), 500);
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_renderResponse($httpResponse, $this->_errorResponse(Mage::getIsDeveloperMode() ? $e->getMessage() : ''), 500);
        }
        return $this;
    }

    protected function _renderResponse(Mage_Core_Controller_Response_Http $response, $data, $httpCode)
    {
        try {
            $content = Mage::helper('core')->jsonEncode($data);
            $response->setBody($content);
            $response->setHttpResponseCode($httpCode);
        } catch (Exception $e) {
            Mage::logException($e);
            $response->setBody('Undefined error');
            $response->setHttpResponseCode(500);
        }
    }

    protected function _process($categoryId, $allMode = false)
    {
        return array(
            'status' => true,
            'results' => $this->_buildResults($categoryId, $allMode)
                ) + $this->_ajaxResponseTemplate;
    }

    protected function _errorResponse($errorMessage = null)
    {
        return array(
            'status' => false,
            'errorMessage' => $this->_helper()->__($errorMessage ? $errorMessage : ("Server error")),
                ) + $this->_ajaxResponseTemplate;
    }

    protected function _buildResults($categoryId, $allMode)
    {
        $categoriesToProcess = $this->_optimization()->loadRelatedCategoriesForAjaxCall(array($categoryId), $allMode);
        if ($categoryId) {
            $categoryArray = array($categoryId);
        } else {
            $categoryArray = array();
        }

        $categoriesToProcess = array_merge(
                $categoryArray, $categoriesToProcess
        );

        return array_map(array($this, '_buildMenuForCategory'), $categoriesToProcess);
    }

    protected function _buildMenuForCategory($categoryOrId)
    {
        return array(
            'type' => $this->_mmCategoryHelper()->getCategoryType($categoryOrId),
            'category_id' => $this->_mmCategoryHelper()->asCategoryId($categoryOrId),
            'html' => $this->_mmCategoryHelper()->renderContainerHtml($categoryOrId),
        );
    }

    /**
     * @return Creatuity_MegaMenu_Model_Catalog_Category_Helper
     */
    protected function _mmCategoryHelper()
    {
        return Mage::getSingleton('creatuity_megamenu/catalog_category_helper');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Optimization
     */
    protected function _optimization()
    {
        return Mage::getSingleton('creatuity_megamenu/optimization');
    }

    /**
     * @return Creatuity_MegaMenu_Model_Cache
     */
    protected function _cache()
    {
        return Mage::getSingleton('creatuity_megamenu/cache');
    }

    /**
     * @return Creatuity_MegaMenu_Helper_Data
     */
    protected function _helper()
    {
        return Mage::helper('creatuity_megamenu');
    }

}
