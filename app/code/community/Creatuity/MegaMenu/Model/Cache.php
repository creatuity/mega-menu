<?php

/**
 * @category   Creatuity
 * @package    megamenu
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
class Creatuity_MegaMenu_Model_Cache
{

    const HOURS_IN_BROWSER_CACHE = 8760;    // 24 * 365
    const FUZE_VALUE_CACHE_KEY = 'megamenu_cache_key';
    const CACHE_PREFIX = 'Creatuity_MegaMenu_';

    public function getFuzeParamName()
    {
        return self::FUZE_VALUE_CACHE_KEY;
    }

    public function getFuzeParamValue()
    {
        return $this->_versionHash();
    }

    protected function _versionHash()
    {
        $hash = $this->loadCache(self::FUZE_VALUE_CACHE_KEY);
        if ($hash === false) {
            $hash = sha1(rand(1, getrandmax()));
            $this->saveCache(self::FUZE_VALUE_CACHE_KEY, $hash);
        }
        return $hash;
    }

    public function setNeverExpireBrowserCacheHeader(Mage_Core_Controller_Request_Http $httpRequest, Mage_Core_Controller_Response_Http $response)
    {
        if (!Mage::app()->useCache('config')) {
            return $this;
        }

        if (!$httpRequest->has(self::FUZE_VALUE_CACHE_KEY)) {
            Mage::log("MegaMenu cache problem: You cannot set never expire browser cache, because this http request has not been secured by the fuze url param.");
            return $this;
        }

        $expires = gmdate('D, d M Y H:i:s \G\M\T', time() + 3600 * self::HOURS_IN_BROWSER_CACHE);
        $maxAge = self::HOURS_IN_BROWSER_CACHE * 3600;

        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Expires', $expires, true);
        $response->setHeader('Cache-Control', "public, max-age={$maxAge}", true);

        return $this;
    }

    public function saveCache($key, $value)
    {
        if (!Mage::app()->useCache('config')) {
            return false;
        }

        $tags = $this->_config()->getCacheTags();
        Mage::app()->saveCache(serialize($value), $this->_getCacheKey($key), $tags, false);
        return true;
    }

    public function loadCache($key)
    {
        if (!Mage::app()->useCache('config')) {
            return false;
        }

        return unserialize(Mage::app()->loadCache($this->_getCacheKey($key)));
    }

    public function _getCacheKey($key)
    {
        return self::CACHE_PREFIX . md5(implode('|', array(
                    Mage::app()->getStore()->getId(),
                    Mage::getDesign()->getPackageName(),
                    Mage::getDesign()->getTheme('template'),
                    Mage::getSingleton('customer/session')->getCustomerGroupId(),
                    $key
        )));
    }

    /**
     * @return Creatuity_MegaMenu_Model_Config
     */
    protected function _config()
    {
        return Mage::getSingleton('creatuity_megamenu/config');
    }

}
