<?php

namespace Realweb;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Realweb\RealwebMainIncludeCategoryTable
 *
 */
Class RealwebMainIncludeCategoryTable extends Main\Entity\DataManager {

    public static function getTableName() {
        return 'realweb_main_include_category';
    }

    public static function getByCode($CODE) {
        return static::getList(array(
                    "filter" => array(
                        "=CODE" => $CODE
                    )
        ));
    }

    /**
     * 
     * @param string $strCode
     * @return \Realweb\Category\Entity
     */
    public static function getCategoryByCode($strCode) {
        $rsRow = self::getByCode($strCode);

        if ($arRow = $rsRow->fetch()) {
            $obEntity = new \Realweb\Category\Entity($arRow);
        } else {
            $arRow = array_fill_keys(array_keys(self::getMap()), '');
            $obEntity = new \Realweb\Category\Entity($arRow);
        }

        return $obEntity;
    }

    public static function getAll() {
        return static::getList();
    }

    public static function getMap() {
        return array(
            'ID' => new Main\Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_CATEGORY_ENTITY_ID_FIELD'),
                    )),
            'CODE' => new Main\Entity\StringField('CODE', array(
                'required' => true,
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_CATEGORY_ENTITY_CODE_FIELD'),
                    )),
            'NAME' => new Main\Entity\TextField('NAME', array(
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_CATEGORY_ENTITY_NAME_FIELD'),
                    )),
        );
    }

    public static function getNames() {
        $arValues = array('');
        foreach (\Realweb\RealwebMainIncludeCategoryTable::getAll() as $arCategory) {
            $arValues[] = $arCategory['NAME'];
        }
        return $arValues;
    }

    public static function getValues() {
        $arValues = array(0);
        foreach (\Realweb\RealwebMainIncludeCategoryTable::getAll() as $arCategory) {
            $arValues[] = $arCategory['ID'];
        }
        return $arValues;
    }

}
