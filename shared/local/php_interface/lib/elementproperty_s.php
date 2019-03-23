<?php

namespace Bitrix\Iblock;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class ElementPropS3Table
 * 
 * Fields:
 * <ul>
 * <li> IBLOCK_ELEMENT_ID int mandatory
 * <li> PROPERTY_22 double optional
 * <li> PROPERTY_23 string optional
 * <li> PROPERTY_24 double optional
 * <li> PROPERTY_35 double optional
 * <li> PROPERTY_36 double optional
 * </ul>
 *
 * @package Bitrix\Iblock
 * */
class ElementPropSTable extends Main\Entity\DataManager {

    protected static $iblockId = 0;

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName() {
        return 'b_iblock_element_prop_s' . self::$iblockId;
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap() {

        $arReturn = array(
            'IBLOCK_ELEMENT_ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'title' => Loc::getMessage('ELEMENT_PROP_S3_ENTITY_IBLOCK_ELEMENT_ID_FIELD'),
            ),
        );

        $rsProperties = PropertyTable::getList(array(
                'filter' => array('IBLOCK_ID' => self::$iblockId),
                'select' => array('ID', 'PROPERTY_TYPE', 'NAME')
        ));

        while ($arProperty = $rsProperties->fetch()) {

            $type = '';
            switch ($arProperty['PROPERTY_TYPE']) {
                case PropertyTable::TYPE_NUMBER:
                    $type = 'float';
                    break;
                case PropertyTable::TYPE_FILE:
                case PropertyTable::TYPE_ELEMENT:
                case PropertyTable::TYPE_SECTION:
                case PropertyTable::TYPE_LIST:
                    $type = 'int';
                    break;
                case PropertyTable::TYPE_STRING:
                default:
                    $type = 'text';
                    break;
            }

            $arReturn['PROPERTY_' . $arProperty['ID']] = array(
                'data_type' => $type,
                'title' => $arProperty['NAME'],
            );
        }


        return $arReturn;
    }

}
