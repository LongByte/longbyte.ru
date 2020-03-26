<?php

namespace Api\Core\Main\File;

/**
 * Class \Api\Core\Main\File\Entity
 * 
 * @method int getId()
 * @method $this setId(int $iId)
 * @method bool hasId()
 * @method \Bitrix\Main\Type\DateTime getTimestampX()
 * @method $this setTimestampX(\Bitrix\Main\Type\DateTime $obTimestampX)
 * @method bool hasTimestampX()
 * @method string getModuleId()
 * @method $this setModuleId(string $strModuleId)
 * @method bool hasModuleId()
 * @method int getHeight()
 * @method $this setHeight(int $iHeight)
 * @method bool hasHeight()
 * @method int getWidth()
 * @method $this setWidth(int $iWidth)
 * @method bool hasWidth()
 * @method int getFileSize()
 * @method $this setFileSize(int $iFileSize)
 * @method bool hasFileSize()
 * @method string getContentType()
 * @method $this setContentType(string $strContentType)
 * @method bool hasContentType()
 * @method string getSubdir()
 * @method $this setSubdir(string $strSubdir)
 * @method bool hasSubdir()
 * @method string getFileName()
 * @method $this setFileName(string $strFileName)
 * @method bool hasFileName()
 * @method string getOriginalName()
 * @method $this setOriginalName(string $strOriginalName)
 * @method bool hasOriginalName()
 * @method string getDescription()
 * @method $this setDescription(string $strDescription)
 * @method bool hasDescription()
 * @method string getHandlerId()
 * @method $this setHandlerId(string $strHandlerId)
 * @method bool hasHandlerId()
 * @method string getExternalId()
 * @method $this setExternalId(string $strExternalId)
 * @method bool hasExternalId()
 */
class Entity extends \Api\Core\Base\Entity {

    protected $_src = null;

    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return array
     */
    public function getFields() {
        return array_keys(static::getModel()::getTable()::getMap());
    }

    /**
     * 
     * @return string
     */
    public function getSrc() {
        if ($this->isExist() && is_null($this->_src)) {
            $uploadDirName = \Bitrix\Main\Config\Option::get('main', 'upload_dir', 'upload');
            $this->_src = '/' . $uploadDirName . '/' . $this->getSubdir() . '/' . $this->getFileName();
        }
        return $this->_src;
    }

    /**
     * 
     * @param string $strSrc
     * @return $this
     */
    protected function _setSrc(string $strSrc) {
        $this->_src = $strSrc;
        return $this;
    }

    /**
     * 
     * @param int $iWidth
     * @param int $iHeight
     * @param int $iMode
     * @return $this
     */
    public function setResize(int $iWidth, int $iHeight, int $iMode = BX_RESIZE_IMAGE_PROPORTIONAL) {
        $arImage = \CFile::ResizeImageGet($this->getId(), array('width' => $iWidth, 'height' => $iHeight), $iMode, true);
        if ($arImage) {
            $this->setWidth($arImage['width']);
            $this->setHeight($arImage['height']);
            $this->_setSrc($arImage['src']);
        }
        return $this;
    }

    /**
     * 
     * @return $this
     */
    public function convertToWebp() {
        if (class_exists('\LongByte\Webp')) {
            $strSrc = $this->getSrc();
            if (strlen($strSrc) > 0) {
                $obWebp = new \LongByte\Webp($this->getSrc());
                $strWebpSrc = $obWebp->getWebpPath();
                if (strlen($strWebpSrc) > 0) {
                    $this->_setSrc($strWebpSrc);
                }
            }
        }
        return $this;
    }

}
