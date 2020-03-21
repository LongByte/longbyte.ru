<?php

namespace Api\Core\Entity;

/**
 * Class \Api\Core\Entity\Section

 */
abstract class Section extends Base {

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
