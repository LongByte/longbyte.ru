<?php

namespace Realweb;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM;

Loc::loadMessages(__FILE__);

class RealwebMainIncludeTable extends Main\Entity\DataManager
{

    const TYPE_TEXT = 'text';
    const TYPE_HTML = 'html';

    public static function getTableName()
    {
        return 'realweb_main_include';
    }

    public static function getByCode($CODE)
    {
        return static::getList(array(
            "filter" => array(
                "=CODE" => $CODE,
            ),
        ));
    }

    public static function getAll()
    {
        return static::getList();
    }

    public static function getScalarFields()
    {
        $arFields = array();
        foreach (self::getMap() as $strId => $obField) {
            if ($obField instanceof \Bitrix\Main\Entity\ScalarField) {
                $arFields[$strId] = $obField;
            }
        }
        return $arFields;
    }

    public static function getMap()
    {
        return array(
            'ID' => new Main\Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_ENTITY_ID_FIELD'),
            )),
            'CODE' => new Main\Entity\StringField('CODE', array(
                'required' => true,
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_ENTITY_CODE_FIELD'),
            )),
            'CATEGORY' => new Main\Entity\IntegerField('CATEGORY', array(
                'required' => false,
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_ENTITY_CATEGORY_FIELD'),
                'default_value' => '',
            )),
            'TEXT' => new Main\Entity\TextField('TEXT', array(
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_ENTITY_PREVIEW_TEXT_FIELD'),
            )),
            'TEXT_TYPE' => new Main\Entity\EnumField('TEXT_TYPE', array(
                'values' => array(self::TYPE_TEXT, self::TYPE_HTML),
                'default_value' => self::TYPE_HTML,
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_ENTITY_PREVIEW_TEXT_TYPE_FIELD'),
            )),
            'DESCRIPTION' => new Main\Entity\TextField('DESCRIPTION', array(
                'title' => Loc::getMessage('REALWEB.MAIN.INCLUDE_ENTITY_DESCRIPTION_FIELD'),
            )),
            'CATEGORY_ENTITY' => new Main\Entity\ReferenceField(
                'CATEGORY_ENTITY', '\Realweb\RealwebMainIncludeCategoryTable', array('=this.CATEGORY' => 'ref.ID'), array('join_type' => 'LEFT')
            ),
        );
    }

    public static function add($arData)
    {
        $rsResult = parent::add($arData);
        global $CACHE_MANAGER;
        $CACHE_MANAGER->ClearByTag("realweb_main_include");
        return $rsResult;
    }

    public static function update($iId, $arData)
    {
        $rsResult = parent::update($iId, $arData);
        global $CACHE_MANAGER;
        $CACHE_MANAGER->ClearByTag("realweb_main_include");
        return $rsResult;
    }

}
