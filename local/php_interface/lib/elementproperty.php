<?php

namespace Bitrix\Iblock;

use Bitrix\Main,
    Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class ElementPropertyTable
 * 
 * Fields:
 * <ul>
 * <li> ID int mandatory
 * <li> IBLOCK_PROPERTY_ID int mandatory
 * <li> IBLOCK_ELEMENT_ID int mandatory
 * <li> VALUE string mandatory
 * <li> VALUE_TYPE enum ('text', 'html') optional default 'text'
 * <li> VALUE_ENUM int optional
 * <li> VALUE_NUM double optional
 * <li> DESCRIPTION string(255) optional
 * <li> IBLOCK_ELEMENT reference to {@link \Bitrix\Iblock\IblockElementTable}
 * <li> IBLOCK_PROPERTY reference to {@link \Bitrix\Iblock\IblockPropertyTable}
 * </ul>
 *
 * @package Bitrix\Iblock
 * */
class ElementPropertyTable extends Main\Entity\DataManager {

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName() {
        return 'b_iblock_element_property';
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap() {
        return array(
            'ID' => array(
                'data_type' => 'integer',
                'primary' => true,
                'autocomplete' => true,
                'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_ID_FIELD'),
            ),
            'IBLOCK_PROPERTY_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_IBLOCK_PROPERTY_ID_FIELD'),
            ),
            'IBLOCK_ELEMENT_ID' => array(
                'data_type' => 'integer',
                'required' => true,
                'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_IBLOCK_ELEMENT_ID_FIELD'),
            ),
            'VALUE' => array(
                'data_type' => 'text',
                'required' => true,
                'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_VALUE_FIELD'),
            ),
            'VALUE_TYPE' => array(
                'data_type' => 'enum',
                'values' => array('text', 'html'),
                'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_VALUE_TYPE_FIELD'),
            ),
            'VALUE_ENUM' => array(
                'data_type' => 'integer',
                'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_VALUE_ENUM_FIELD'),
            ),
            'VALUE_NUM' => array(
                'data_type' => 'float',
                'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_VALUE_NUM_FIELD'),
            ),
            'DESCRIPTION' => array(
                'data_type' => 'string',
                'validation' => array(__CLASS__, 'validateDescription'),
                'title' => Loc::getMessage('ELEMENT_PROPERTY_ENTITY_DESCRIPTION_FIELD'),
            ),
            'IBLOCK_ELEMENT' => array(
                'data_type' => 'Bitrix\Iblock\ElementTable',
                'reference' => array('=this.IBLOCK_ELEMENT_ID' => 'ref.ID'),
            ),
            'IBLOCK_PROPERTY' => array(
                'data_type' => 'Bitrix\Iblock\PropertyTable',
                'reference' => array('=this.IBLOCK_PROPERTY_ID' => 'ref.ID'),
            ),
        );
    }

    /**
     * Returns validators for DESCRIPTION field.
     *
     * @return array
     */
    public static function validateDescription() {
        return array(
            new Main\Entity\Validator\Length(null, 255),
        );
    }

}
