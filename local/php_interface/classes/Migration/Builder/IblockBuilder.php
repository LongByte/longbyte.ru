<?php

namespace Migration\Builder;

class IblockBuilder extends \WS\ReduceMigrations\Builder\IblockBuilder
{

    public function GetIblockType($IBLOCK_TYPE_ID)
    {
        $row = \Bitrix\Iblock\TypeTable::getRow(array(
            'select' => array('ID'),
            'filter' => array(
                '=ID' => $IBLOCK_TYPE_ID,
            ),
        ));
        return $row;
    }

    public function GetIblock($IBLOCK_TYPE_ID, $CODE)
    {
        $row = \Bitrix\Iblock\IblockTable::getRow(array(
            'select' => array('ID', 'IBLOCK_TYPE_ID', 'CODE', 'NAME'),
            'filter' => array(
                '=IBLOCK_TYPE_ID' => $IBLOCK_TYPE_ID,
                '=CODE' => $CODE,
            ),
        ));
        return $row;
    }

    public function PropertyExist($IBLOCK_ID, $CODE)
    {
        $property = \CIBlockProperty::GetList(null, array(
            'IBLOCK_ID' => $IBLOCK_ID,
            'CODE' => $CODE,
        ))->Fetch();

        if (!$property) {
            return false;
        }
        return true;
    }

    public function GetPropertiesByIblockId($IBLOCK_ID)
    {
        $arProps = array();
        $propertys = \CIBlockProperty::GetList(null, array(
            'IBLOCK_ID' => $IBLOCK_ID,
        ));

        while ($property = $propertys->fetch()) {
            $CODE = (strlen($property['CODE']) > 0 ? $property['CODE'] : $property['ID']);
            $arProps[$CODE] = $property;
        }
        return $arProps;
    }

}
