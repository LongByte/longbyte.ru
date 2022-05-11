<?php

namespace Api\Files\Element;

/**
 * Class \Api\Files\Element\Entity
 */
class Entity extends \Api\Core\Iblock\Element\Entity
{

    protected static array $arFields = array(
        'ID',
        'NAME',
        'PREVIEW_PICTURE',
    );

    protected static array $arProps = array(
        'FILE',
    );

    protected ?\Api\Core\Main\File\Entity $_obFile = null;

    public static function getModel(): string
    {
        return Model::class;
    }

    private function _getFile(): \Api\Core\Main\File\Entity
    {
        if (is_null($this->_obFile)) {
            if ($this->getFile() > 0) {
                $this->_obFile = new \Api\Core\Main\File\Entity($this->getFile());
            } elseif ($this->getPreviewPicture() > 0) {
                $this->_obFile = $this->getPreviewPictureFile();
            }
        }
        return $this->_obFile;
    }

    private function _isImage(): bool
    {
        if (!is_null($this->_getFile())) {
            if ($this->_getFile()->getIOFile()->isExists()) {
                return strpos($this->_getFile()->getIOFile()->getContentType(), 'image/') !== false;
            }
        }
        return false;
    }

    private function _getFileSize(): string
    {
        if (!is_null($this->_getFile())) {
            return $this->_getFile()->getFileSizePrint();
        }
        return '';
    }

    public function toArray(): array
    {
        $arData = parent::toArray();
        $arData['is_image'] = $this->_isImage();
        $arData['file_size'] = $this->_getFileSize();
        $arData['file_src'] = $this->_getFile()->convertToWebp()->getSrc();
        $arData['preview_picture'] = $this->getPreviewPictureFile()->setResize(228, 190)->convertToWebp()->getSrc();
        return $arData;
    }

}
