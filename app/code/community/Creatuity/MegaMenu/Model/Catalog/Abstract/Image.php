<?php

/**
 *
 * @category   Creatuity
 * @package    internals
 * @copyright  Copyright (c) 2008-2014 Creatuity Corp. (http://www.creatuity.com)
 * @license    http://creatuity.com/license
 */
abstract class Creatuity_MegaMenu_Model_Catalog_Abstract_Image
{

    abstract protected function _getEntityName();

    protected function _entityImageUrl(Varien_Object $entity, $width, $height, $entityField)
    {
        $entityPath = $entity->getData($entityField);

        if (!$entityPath) {
            return null;
        }

        $dstPath = array(
            'cache',
            Mage::app()->getStore()->getId(),
            $entityField,
            $width . 'x' . $height,
            $entityPath
        );

        $dstFile = $this->getBaseMediaPath() . DS . implode(DS, $dstPath);
        if (!file_exists($dstFile)) {
            $srcPath = array(
                $entityPath
            );

            $srcFile = $this->getBaseMediaPath() . DS . implode(DS, $srcPath);
            if (!file_exists($srcFile)) {
                return null;
            }
            $image = new Varien_Image($srcFile);
            $image->keepAspectRatio(true);
            $image->keepTransparency(true);
            $image->keepFrame(true);
            $image->constrainOnly(false);
            $image->backgroundColor(array(255, 255, 255));
            $image->resize($width, $height);
            $image->save($dstFile);
        }

        return $this->getBaseMediaUrl() . '/' . implode('/', $dstPath);
    }

    public function getBaseMediaPath()
    {
        return Mage::getBaseDir('media') . DS . 'catalog' . DS . $this->_getEntityName();
    }

    public function getBaseMediaUrl()
    {
        return Mage::getBaseUrl('media') . 'catalog/' . $this->_getEntityName();
    }

}
