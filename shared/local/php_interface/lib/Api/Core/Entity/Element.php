<?php

namespace Api\Core\Entity;

/**
 * Class \Api\Core\Entity\Element
 * 
 */
abstract class Element extends Base {

    /**
     * @var int
     */
    protected $_iblockId = 0;

    /**
     * 
     * @return int
     */
    public static function getIblockId() {
        return $this->_iblockId;
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
        $arData['IBLOCK_ID'] = static::getIblockId();
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
