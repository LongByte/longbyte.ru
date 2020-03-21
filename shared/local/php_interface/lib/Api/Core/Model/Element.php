<?php

namespace Api\Core\Model;

use Bitrix\Main\Loader;

/**
 * Class \Api\Core\Model\Element
 */
abstract class Element extends Base {

    /**
     * @var int
     */
    protected static $_iblockId = 0;

    public static function getTable() {
        
    }

    /**
     * 
     * @return int
     */
    public static function getIblockId() {
        return static::$_iblockId;
    }

    /**
     * 
     * @param array $arFilter
     * @return \Api\Core\Entity\Base
     */
    public static function getOne(array $arFilter = array()) {

        Loader::includeModule('iblock');

        $arFilter['IBLOCK_ID'] = static::getIblockId();
        $arSelect = static::getEntity()::getFields();
        $arSelect[] = 'IBLOCK_ID';

        $rsElement = \CIBlockElement::GetList(
                array('SORT' => 'ASC', 'NAME' => 'ASC', 'ID' => 'ASC'),
                $arFilter,
                false,
                array('nTopCount' => 1),
                $arSelect
        );

        $obElement = $rsElement->GetNextElement(false, true);

        if ($obElement) {

            $obEntity = static::_getEntityFromElement($obElement);
            return $obEntity;
        }

        return null;
    }

    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iPageSize = 0, int $iNumPage = 0) {

        Loader::includeModule('iblock');

        $arFilter['IBLOCK_ID'] = static::getIblockId();
        $arSelect = static::getEntity()::getFields();
        $arSelect[] = 'IBLOCK_ID';

        $arNavigation = array();
        if ($iLimit > 0) {
            $arNavigation['nTopCount'] = $iLimit;
        }
        if ($iPageSize > 0) {
            $arNavigation['nPageSize'] = $iPageSize;
            if ($iNumPage > 0) {
                $arNavigation['iNumPage'] = $iNumPage;
            }
        }

        $strCollectionClass = static::getCollection();
        $obCollection = new $strCollectionClass();

        $rsElement = \CIBlockElement::GetList(
                array('SORT' => 'ASC', 'NAME' => 'ASC', 'ID' => 'ASC'),
                $arFilter,
                false,
                $arNavigation ?: false,
                $arSelect
        );

        while ($obElement = $rsElement->GetNextElement(false, true)) {

            $obEntity = static::_getEntityFromElement($obElement);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    private static function _getEntityFromElement($obElement) {
        $arElement = $obElement->GetFields();
        $arProperties = $obElement->GetProperties();

        $arElement = static::_getFromTilda($arElement);
        
        $strEntityClass = static::getEntity();
        $obEntity = new $strEntityClass($arElement['ID'], $arElement);
        return $obEntity;
    }

}
