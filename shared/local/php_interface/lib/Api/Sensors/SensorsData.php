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
            'UF_SYSTEM_ID' => new Main\Entity\IntegerField('UF_SYSTEM_ID', array(
                'required' => true,
                'title' => 'Система',
                )),
            'UF_DATE' => new Main\Entity\DatetimeField('UF_DATE', array(
                'required' => true,
                'title' => 'Дата',
                )),
            'UF_SENSOR_APP' => new Main\Entity\StringField('UF_SENSOR_APP', array(
                'title' => 'Приложение',
                )),
            'UF_SENSOR_DEVICE' => new Main\Entity\StringField('UF_SENSOR_DEVICE', array(
                'title' => 'Устройство',
                )),
            'UF_SENSOR_NAME' => new Main\Entity\StringField('UF_SENSOR_NAME', array(
                'required' => true,
                'title' => 'Сенсор',
                )),
            'UF_SENSOR_VALUE_MIN' => new Main\Entity\FloatField('UF_SENSOR_VALUE_MIN', array(
                'required' => true,
                'title' => 'Минимальное значение',
                )),
            'UF_SENSOR_VALUE_AVG' => new Main\Entity\FloatField('UF_SENSOR_VALUE_AVG', array(
                'required' => true,
                'title' => 'Среднее значение',
                )),
            'UF_SENSOR_VALUE_MAX' => new Main\Entity\FloatField('UF_SENSOR_VALUE_MAX', array(
                'required' => true,
                'title' => 'Максимальное значение',
                )),
            'UF_SENSOR_VALUES' => new Main\Entity\IntegerField('UF_SENSOR_VALUES', array(
                'required' => true,
                'title' => 'Количество значений',
                )),
            'UF_SENSOR_UNIT' => new Main\Entity\StringField('UF_SENSOR_UNIT', array(
                'title' => 'Единицы измерений',
                )),
        );
    }

}
