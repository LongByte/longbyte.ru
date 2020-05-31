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

        $arParams = array(
            'select' => $arSelect,
            'filter' => $arFilter,
            'order' => array('SORT' => 'ASC', 'NAME' => 'ASC', 'ID' => 'ASC'),
        );

        if (in_array('SECTION_CODE_PATH', $arSelect)) {
            self::_appendSectionCodePath($arParams);
        }

        $dbSectionTable = self::_getTableEntity();
        $arSection = $dbSectionTable::getRow($arParams);

        if ($arSection) {

            $obEntity = static::_getEntityFromSection($arSection);
            return $obEntity;
        } else {
            return static::_getEntityFromSection(array());
        }

        return null;
    }

    /**
     * 
     * @param array $arFilter
     * @param int $iLimit
     * @param int $iOffset
     * @param array $arParams
     * @return \Api\Core\Base\Collection
     */
    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iOffset = 0, array $arParams = array()) {

        Loader::includeModule('iblock');

        $arFilter['IBLOCK_ID'] = static::getIblockId();

        $strCollectionClass = static::getEntity()::getCollection();
        $obCollection = new $strCollectionClass();

        $arParams['filter'] = $arFilter;

        if (!(is_array($arParams['select']) && count($arParams['select']) > 0)) {
            $arParams['select'] = static::getEntity()::getFields();
        }
        if (!in_array('IBLOCK_ID', $arParams['select'])) {
            $arParams['select'][] = 'IBLOCK_ID';
        }
        if (is_array($arParams['select_add']) && count($arParams['select_add']) > 0) {
            $arParams['select'] = array_merge($arParams['select'], $arParams['select_add']);
            unset($arParams['select_add']);
        }

        if (empty($arParams['order'])) {
            $arParams['order'] = array('SORT' => 'ASC', 'NAME' => 'ASC', 'ID' => 'ASC');
        }

        if ($iLimit > 0) {
            $arParams['limit'] = $iLimit;
        }

        if ($iOffset > 0) {
            $arParams['offset'] = $iOffset;
        }

        if (is_array($arParams['runtime_add']) && count($arParams['runtime_add']) > 0) {
            if (!is_array($arParams['runtime'])) {
                $arParams['runtime'] = array();
            }
            $arParams['runtime'] = array_merge($arParams['runtime'], $arParams['runtime_add']);
            unset($arParams['runtime_add']);
        }

        if (in_array('SECTION_CODE_PATH', $arParams['select'])) {
            self::_appendSectionCodePath($arParams);
        }

        $dbSectionTable = self::_getTableEntity();
        $rsSections = $dbSectionTable::getList($arParams);

        while ($arSection = $rsSections->fetch()) {

            $obEntity = static::_getEntityFromSection($arSection);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    /**
     * 
     * @param array $arSections
     * @return \Api\Core\Base\Collection
     */
    public static function getFromArray(array $arSections) {

        $strCollectionClass = static::getEntity()::getCollection();
        $obCollection = new $strCollectionClass();

        foreach ($arSections as $arSection) {
            $obEntity = static::_getEntityFromSection($arSection);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

    /**
     * 
     * @return type
     */
    protected static function _getTableEntity() {
        return \Bitrix\Iblock\Model\Section::compileEntityByIblock(self::getIblockId());
    }

    /**
     * 
     * @param array $arParams
     */
    protected static function _appendSectionCodePath(array &$arParams) {
        $arParams['runtime']['SubSectionTable'] = array(
            'data_type' => \Bitrix\Iblock\SectionTable::getEntity(),
            'join_type' => 'inner',
            'reference' => array(
                '<=ref.LEFT_MARGIN' => 'this.LEFT_MARGIN',
                '>=ref.RIGHT_MARGIN' => 'this.RIGHT_MARGIN',
                '=ref.IBLOCK_ID' => 'this.IBLOCK_ID'
            ),
        );
        $arParams['runtime']['SECTION_CODE_PATH'] = array(
            'expression' => array('GROUP_CONCAT(%s ORDER BY %s ASC SEPARATOR \'/\')', 'SubSectionTable.CODE', 'SubSectionTable.LEFT_MARGIN'),
        );
    }

    /**
     * 
     * @param array $arSection
     * @return \Api\Core\Iblock\Section\Entity
     */
    protected static function _getEntityFromSection($arSection) {

        $strEntityClass = static::getEntity();
        /** @var \Api\Core\Iblock\Section\Entity $obEntity */
        $obEntity = new $strEntityClass($arSection['ID'], $arSection);

        return $obEntity;
    }

}
