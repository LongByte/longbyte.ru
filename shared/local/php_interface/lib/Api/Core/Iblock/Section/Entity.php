<?php

namespace Api\Core\Iblock\Section;

/**
 * Class \Api\Core\Iblock\Section\Entity

 */
abstract class Entity extends Base {

    /**
     *
     * @var \Api\Core\Main\File\Entity
     */
    protected $_obPicture = null;

    /**
     *
     * @var \Api\Core\Main\File\Entity 
     */
    protected $_obDetailPicture = null;

    /**
     * 
     * @return \Api\Core\Main\File\Entity
     */
    public function getPictureFile() {
        $iFile = 0;
        if (is_null($this->_obPicture)) {
            if ($this->hasPicture()) {
                $iFile = $this->getPicture();
            }
            $this->_obPicture = new \Api\Core\Main\File\Entity($iFile);
        }
        return $this->_obPicture;
    }

    /**
     * 
     * @return \Api\Core\Main\File\Entity
     */
    public function getDetailPictureFile() {
        $iFile = 0;
        if (is_null($this->_obDetailPicture)) {
            if ($this->hasDetailPicture()) {
                $iFile = $this->getDetailPicture();
            }
            $this->_obDetailPicture = new \Api\Core\Main\File\Entity($iFile);
        }
        return $this->_obDetailPicture;
    }

    /**
     * @return $this
     */
    public function save() {
        $arData = array();
        foreach ($this->getFields() as $strField) {
            $arData[$strField] = $this->_data[$strField];
        }
        unset($arData['ID']);
        $arData['IBLOCK_ID'] = static::getIblockId();
        $obSection = new \CIBlockSection();
        if ($this->isExist()) {
            $obSection->Update($this->getId(), $arData);
            $this->_data = null;
            $this->getData();
        } else {
            if ($iId = $obSection->Add($arData)) {
                $this->setId($iId);
                $this->_primary = $iId;
            }
        }

        return $this;
    }

    public function delete() {
        if ($this->isExist()) {
            $iId = $this->getId();
            \CIBlockSection::Delete($iId);
            $this->setId(0);
            $this->_primary = null;
            $this->_exist = false;
            $this->_changed = true;
        }
        return $this;
    }

}
