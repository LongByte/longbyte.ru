<?php

namespace Api\Core\Iblock\Section;

/**
 * Class \Api\Core\Iblock\Section\Entity

 */
abstract class Entity extends \Api\Core\Base\Entity {

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
     * @return string
     */
    public function getSectionPageUrl() {
        $arReplaceFrom = array('#SITE_DIR#');
        $arReplaceTo = array('/');
        if ($this->hasCode()) {
            $arReplaceFrom[] = '#CODE#';
            $arReplaceTo[] = $this->getCode();
            $arReplaceFrom[] = '#SECTION_CODE#';
            $arReplaceTo[] = $this->getCode();
        }
        if ($this->hasId()) {
            $arReplaceFrom[] = '#ID#';
            $arReplaceTo[] = $this->getId();
            $arReplaceFrom[] = '#SECTION_ID#';
            $arReplaceTo[] = $this->getId();
        }
        if ($this->hasSectionCodePath()) {
            $arReplaceFrom[] = '#SECTION_CODE_PATH#';
            $arReplaceTo[] = $this->getSectionCodePath();
        }
        $url = str_replace($arReplaceFrom, $arReplaceTo, $this->getUrlTemplate());

        return preg_replace("'(?<!:)/+'s", "/", $url);
    }

    /**
     * @return string
     */
    public function getUrlTemplate() {
        if (is_null($this->_url_template)) {
            if ($obIblock = $this->getIblock()) {
                $this->_url_template = $obIblock->getSectionPageUrl();
            }
        }

        return $this->_url_template;
    }

    /**
     * @return null|\Api\Core\Iblock\Iblock\Entity
     */
    public function getIblock() {
        if (is_null($this->_iblock)) {
            $this->_iblock = \Api\Core\Iblock\Iblock\Model::getOne(array('ID' => static::getModel()::getIblockId()));
        }

        return $this->_iblock;
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

    /**
     * 
     * @return $this
     */
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
