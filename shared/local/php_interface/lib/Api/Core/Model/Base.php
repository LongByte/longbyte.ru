<?php

namespace Api\Core\Model;

/**
 * Class \Api\Core\Model\Base
 */
abstract class Base {

    abstract protected static function getTable();

    abstract protected static function getEntity();

    abstract protected static function getCollection();

    /**
     * 
     * @param array $arFilter
     * @return \Api\Core\Entity\Base
     */
    public static function getOne(array $arFilter = array()) {
        $arRow = static::getTable()::getRow(array(
                'filter' => $arFilter,
        ));

        if ($arRow) {

            $primaryField = static::getTable()::getEntity()->getPrimary();
            if (is_array($primaryField)) {
                foreach ($primaryField as $strField) {
                    $primary[$strField] = array_key_exists($strField, $arRow) ? $arRow[$strField] : null;
                }
            } else {
                $primary = $arRow[$primaryField];
            }

            $strEntityClass = static::getEntity();
            $obEntity = new $strEntityClass($primary, $arRow);
            return $obEntity;
        }

        return null;
    }

    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iOffset = 0) {

        $arParams = array(
            'filter' => $arFilter,
        );
        if ($iLimit > 0) {
            $arParams['limit'] = $iLimit;
        }
        if ($iOffset > 0) {
            $arParams['offset'] = $iOffset;
        }

        $primaryField = static::getTable()::getEntity()->getPrimary();
        $arRows = static::getTable()::getList(array(
                'filter' => $arFilter,
            ))->fetchAll();

        $strCollectionClass = static::getCollection();
        $obCollection = new $strCollectionClass();

        foreach ($arRows as $arRow) {
            if (is_array($primaryField)) {
                foreach ($primaryField as $strField) {
                    $primary[$strField] = array_key_exists($strField, $arRow) ? $arRow[$strField] : null;
                }
            } else {
                $primary = $arRow[$primaryField];
            }

            $strEntityClass = static::getEntity();
            $obEntity = new $strEntityClass($primary, $arRow);
            $obCollection->addItem($obEntity);
        }

        return $obCollection;
    }

}
