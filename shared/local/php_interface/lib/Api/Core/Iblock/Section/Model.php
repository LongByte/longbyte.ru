<?php

namespace Api\Core\Iblock\Section;

use Bitrix\Main\Loader;

/**
 * Class \Api\Core\Iblock\Section\Model
 */
abstract class Model extends \Api\Core\Base\Model {

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

        $dbSectionTable = self::_getTableEntity();
        $arSection = $dbSectionTable::getRow(array(
                'select' => $arSelect,
                'filter' => $arFilter,
                'order' => array('SORT' => 'ASC', 'NAME' => 'ASC', 'ID' => 'ASC'),
        ));

        if ($arSection) {

            $obEntity = static::_getEntityFromSection($arSection);
            return $obEntity;
        }

        return null;
    }

    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iOffset = 0) {

        Loader::includeModule('iblock');

        $arFilter['IBLOCK_ID'] = static::getIblockId();
        $arSelect = static::getEntity()::getFields();
        $arSelect[] = 'IBLOCK_ID';

        $strCollectionClass = static::getEntity()::getCollection();
        $obCollection = new $strCollectionClass();

        $arParams = array(
            'select' => $arSelect,
            'filter' => $arFilter,
            'order' => array('SORT' => 'ASC', 'NAME' => 'ASC', 'ID' => 'ASC'),
        );

        if ($iLimit > 0) {
            $arParams['limit'] = $iLimit;
        }

        if ($iOffset > 0) {
            $arParams['offset'] = $iOffset;
        }

        $dbSectionTable = self::_getTableEntity();
        $rsSections = $dbSectionTable::getList($arParams);

        while ($arSection = $rsSections->fetch()) {

            $obEntity = static::_getEntityFromElement($arSection);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    /**
     * 
     * @return type
     */
    private static function _getTableEntity() {
        return \Bitrix\Iblock\Model\Section::compileEntityByIblock(self::getIblockId());
    }

    /**
     * 
     * @param array $arSection
     * @return \Api\Core\Iblock\Section\Entity
     */
    private static function _getEntityFromSection($arSection) {

        $strEntityClass = static::getEntity();
        /** @var \Api\Core\Iblock\Section\Entity $obEntity */
        $obEntity = new $strEntityClass($arSection['ID'], $arSection);

        return $obEntity;
    }

}
