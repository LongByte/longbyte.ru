<?php

namespace Api\Sensors;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\SensorsSystemTable
 */
class SensorsDataTable extends Main\Entity\DataManager {

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
            'UF_SENSOR_ID' => new Main\Entity\IntegerField('UF_SENSOR_ID', array(
                'required' => true,
                'title' => 'Сенсор',
                )),
            'SENSOR' => new Main\Entity\ReferenceField(
                'SENSOR',
                '\Api\Sensors\SensorsSensorsTable',
                array('this.UF_SENSOR_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),
            'UF_DATE' => new Main\Entity\DatetimeField('UF_DATE', array(
                'required' => true,
                'title' => 'Дата',
                )),
            'UF_SENSOR_VALUE_MIN' => new Main\Entity\FloatField('UF_SENSOR_VALUE_MIN', array(
                'title' => 'Минимальное значение',
                )),
            'UF_SENSOR_VALUE' => new Main\Entity\FloatField('UF_SENSOR_VALUE', array(
                'required' => true,
                'title' => 'Значение',
                )),
            'UF_SENSOR_VALUE_MAX' => new Main\Entity\FloatField('UF_SENSOR_VALUE_MAX', array(
                'title' => 'Максимальное значение',
                )),
            'UF_SENSOR_VALUES' => new Main\Entity\IntegerField('UF_SENSOR_VALUES', array(
                'title' => 'Количество значений',
                )),
        );
    }

}
