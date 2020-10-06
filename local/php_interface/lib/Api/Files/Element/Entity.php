<?php

namespace Api\Files\Element;

/**
 * Class \Api\Files\Element\Entity
 * 
 */
class Entity extends \Api\Core\Iblock\Element\Entity {

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'ID',
        'NAME',
        'PREVIEW_PICTURE',
    );

    /**
     * @var array
     */
    protected static $arProps = array(
        'FILE',
    );

    /**
     *
     * @var \Api\Core\Main\File\Entity 
     */
    protected $_obFile = null;

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return Model::class;
    }

    /**
     * 
     * @return \Api\Core\Main\File\Entity 
     */
    private function _getFile(): \Api\Core\Main\File\Entity {
        if (is_null($this->_obFile)) {
            if ($this->getFile() > 0) {
                $this->_obFile = new \Api\Core\Main\File\Entity($this->getFile());
            } elseif ($this->getPreviewPicture() > 0) {
                $this->_obFile = $this->getPreviewPictureFile();
            }
        }
        return $this->_obFile;
    }

    /**
     * 
     * @return boolean
     */
    private function _isImage(): bool {
        if (!is_null($this->_getFile())) {
            if ($this->_getFile()->getIOFile()->isExists()) {
                return strpos($this->_getFile()->getIOFile()->getContentType(), 'image/') !== false;
            }
        }
        return false;
    }

    /**
     * 
     * @return string
     */
    private function _getFileSize(): string {
        if (!is_null($this->_getFile())) {
            return $this->_getFile()->getFileSizePrint();
        }
        return '';
    }

    /**
     * 
     * @return array
     */
    public function toArray(): array {
        $arData = parent::toArray();
        $arData['is_image'] = $this->_isImage();
        $arData['file_size'] = $this->_getFileSize();
        $arData['file_src'] = $this->_getFile()->convertToWebp()->getSrc();
        $arData['preview_picture'] = $this->getPreviewPictureFile()->setResize(228, 190)->convertToWebp()->getSrc();
        return $arData;
    }

}
