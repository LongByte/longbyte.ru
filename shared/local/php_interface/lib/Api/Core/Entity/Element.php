<?php

namespace Api\Core\Entity;

/**
 * Class \Api\Core\Entity\Element
 * 
 */
abstract class Element extends Base {

    /**
     *
     * @var \Api\Core\Entity\File 
     */
    protected $_obPreviewPicture = null;

    /**
     *
     * @var \Api\Core\Entity\File 
     */
    protected $_obDetailPicture = null;

    /**
     * @var array
     */
    protected static $arProps = array();

    /**
     * 
     * @return \Api\Core\Entity\File
     */
    public function getPreviewPictureFile() {
        $iFile = 0;
        if (is_null($this->_obPreviewPicture)) {
            if ($this->hasPreviewPicture()) {
                $iFile = $this->getPreviewPicture();
            }
            $this->_obPreviewPicture = new \Api\Core\Entity\File($iFile);
        }
        return $this->_obPreviewPicture;
    }

    /**
     * 
     * @return \Api\Core\Entity\File
     */
    public function getDetailPictureFile() {
        $iFile = 0;
        if (is_null($this->_obDetailPicture)) {
            if ($this->hasDetailPicture()) {
                $iFile = $this->getDetailPicture();
            }
            $this->_obDetailPicture = new \Api\Core\Entity\File($iFile);
        }
        return $this->_obDetailPicture;
    }

    /**
     * 
     * @return array
     */
    public function getProps() {
        return static::$arProps;
    }

    /**
     * 
     * @return null|array
     */
    public function getData() {
        if (is_null($this->_data)) {
            $this->_data = array_fill_keys($this->getFields(), '');
            if (!is_null($this->_primary)) {
                $_arData = static::getModel()::getOneAsArray(array('ID' => $this->_primary));
                if ($_arData) {
                    $this->_data = $_arData;
                    $this->_exist = true;
                }
            }
        }
        return $this->_data;
    }

    /**
     * 
     * @return $this
     */
    public function save() {

        $arData = array();
        foreach ($this->getFields() as $strField) {
            $arData[$strField] = $this->_data[$strField];
        }

        $arProperties = array();

        foreach ($this->getProps() as $strProperty) {
            $arProperties[$strProperty] = $this->_data[$strProperty];
        }

        unset($arData['ID']);
        $arData['IBLOCK_ID'] = static::getModel()::getIblockId();
        $iId = $this->getId();

        $el = new \CIBlockElement;

        if (intval($iId) > 0) {
            $el->Update($iId, $arData);
            $this->_data = null;
            $this->getData();
        } else {
            $iId = $el->Add($arData);
            $this->setId($iId);
            $this->_primary = $iId;
        }

        if ($this->getId() > 0) {
            \CIBlockElement::SetPropertyValuesEx($this->getId(), static::getModel()::getIblockId(), $arProperties);
            $this->_exist = true;
            $this->_changed = false;
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
            \CIBlockElement::Delete($iId);
            $this->setId(0);
            $this->_primary = null;
            $this->_exist = false;
            $this->_changed = true;
        }
        return $this;
    }

    public function counterInc() {
        $iId = $this->getId();
        if (intval($iId) > 0) {
            \CIBlockElement::CounterInc($iId);
        }

        return $this;
    }

}
