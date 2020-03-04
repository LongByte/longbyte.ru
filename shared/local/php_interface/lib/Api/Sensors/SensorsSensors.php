<?php

namespace Api\Sensors;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\SensorsSensorsTable
 */
class SensorsSensorsTable extends Main\Entity\DataManager {

    /**
     * Returns DB table name for entity.
     *
     * @return string
     */
    public static function getTableName() {
        return 'sensors_sensors';
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
            'UF_ACTIVE' => new Main\Entity\BooleanField('UF_ACTIVE', array(
                'title' => 'Активность',
                )),
            'UF_SYSTEM_ID' => new Main\Entity\IntegerField('UF_SYSTEM_ID', array(
                'required' => true,
                'title' => 'Система',
                )),
            'SYSTEM' => new Main\Entity\ReferenceField(
                'SYSTEM',
                '\Api\Sensors\SensorsSystemTable',
                array('this.UF_SYSTEM_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),
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
            'UF_SENSOR_UNIT' => new Main\Entity\StringField('UF_SENSOR_UNIT', array(
                'title' => 'Единицы измерений',
                )),
            'UF_ALERT_VALUE_MIN' => new Main\Entity\FloatField('UF_ALERT_VALUE_MIN', array(
                'title' => 'Минимальное допустимое значение',
                )),
            'UF_ALERT_VALUE_MAX' => new Main\Entity\FloatField('UF_ALERT_VALUE_MAX', array(
                'title' => 'Максимальное допустимое значение',
                )),
        );
    }

}
