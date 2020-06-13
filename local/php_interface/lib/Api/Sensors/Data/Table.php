<?php

namespace Api\Sensors\Data;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\Data\Table
 */
class Table extends Main\Entity\DataManager {

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName() {
        return 'sensors_data';
    }

    public static function getScalarFields() {
        $arFields = array();
        foreach (self::getMap() as $strId => $obField) {
            if ($obField instanceof \Bitrix\Main\Entity\ScalarField) {
                $arFields[$strId] = $obField;
            }
        }
        return $arFields;
    }

    /**
     * Returns entity map definition.
     *
     * @return array
     */
    public static function getMap() {
        return array(
            'ID' => new Main\Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true,
                'title' => 'ID',
                )),
            'SENSOR_ID' => new Main\Entity\IntegerField('UF_SENSOR_ID', array(
                'required' => true,
                'title' => 'Сенсор',
                )),
            'SENSOR' => new Main\Entity\ReferenceField(
                'SENSOR',
                '\Api\Sensors\Sensor\Table',
                array('this.SENSOR_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),
            'DATE' => new Main\Entity\DatetimeField('UF_DATE', array(
                'required' => true,
                'title' => 'Дата',
                )),
            'VALUE_MIN' => new Main\Entity\FloatField('UF_VALUE_MIN', array(
                'title' => 'Минимальное значение',
                )),
            'VALUE_AVG' => new Main\Entity\FloatField('UF_VALUE_AVG', array(
                'title' => 'Значение',
                )),
            'VALUE_MAX' => new Main\Entity\FloatField('UF_VALUE_MAX', array(
                'title' => 'Максимальное значение',
                )),
            'VALUES_COUNT' => new Main\Entity\IntegerField('UF_VALUES_COUNT', array(
                'title' => 'Количество значений',
                )),
            'VALUE' => new Main\Entity\FloatField('UF_VALUE', array(
                'title' => 'Текущее/последнее значение',
                )),
        );
    }

}
