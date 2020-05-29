<?php

namespace Api\Sensors\Sensor;

use Bitrix\Main;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\Sensor\Table
 */
class Table extends Main\Entity\DataManager {

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
            'ACTIVE' => new Main\Entity\BooleanField('UF_ACTIVE', array(
                'title' => 'Активность',
                )),
            'SYSTEM_ID' => new Main\Entity\IntegerField('UF_SYSTEM_ID', array(
                'required' => true,
                'title' => 'Система',
                )),
            'SYSTEM' => new Main\Entity\ReferenceField(
                'SYSTEM',
                '\Api\Sensors\System\Table',
                array('this.SYSTEM_ID' => 'ref.ID'),
                array('join_type' => 'INNER')
            ),
            'SENSOR_APP' => new Main\Entity\StringField('UF_SENSOR_APP', array(
                'title' => 'Приложение',
                )),
            'SENSOR_DEVICE' => new Main\Entity\StringField('UF_SENSOR_DEVICE', array(
                'title' => 'Устройство',
                )),
            'SENSOR_NAME' => new Main\Entity\StringField('UF_SENSOR_NAME', array(
                'required' => true,
                'title' => 'Сенсор',
                )),
            'SENSOR_UNIT' => new Main\Entity\StringField('UF_SENSOR_UNIT', array(
                'title' => 'Единицы измерений',
                )),
            'ALERT_VALUE_MIN' => new Main\Entity\FloatField('UF_ALERT_VALUE_MIN', array(
                'title' => 'Минимальное допустимое значение',
                )),
            'ALERT_VALUE_MAX' => new Main\Entity\FloatField('UF_ALERT_VALUE_MAX', array(
                'title' => 'Максимальное допустимое значение',
                )),
            'VISUAL_MIN' => new Main\Entity\FloatField('UF_VISUAL_MIN', array(
                'title' => 'Минимум на графике',
                )),
            'VISUAL_MAX' => new Main\Entity\FloatField('UF_VISUAL_MAX', array(
                'title' => 'Максимум на графике',
                )),
            'OFF_ALERT' => new Main\Entity\DateTimeField('UF_OFF_ALERT', array(
                'title' => 'Отключить оповещение до',
                )),
        );
    }

}
