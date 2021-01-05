<?php

namespace Api\Sensors\Sensor;

use Bitrix\Main\ORM;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\Sensor\Table
 */
class Table extends ORM\Data\DataManager {

    const MODE_AVG = 0;
    const MODE_EACH = 1;
    const MODE_EACH_LAST_DAY = 2;

    /**
     *
     * @return string
     */
    public static function getTableName(): string {
        return 'sensors_sensors';
    }

    /**
     * 
     * @return array
     */
    public static function getScalarFields(): array {
        $arFields = array();
        foreach (self::getMap() as $strId => $obField) {
            if ($obField instanceof ORM\Fields\ScalarField) {
                $arFields[$strId] = $obField;
            }
        }
        return $arFields;
    }

    /**
     *
     * @return array
     */
    public static function getMap() {
        return array(
            'ID' => (new ORM\Fields\IntegerField('ID'))->configureAutocomplete()->configurePrimary()->configureTitle('ID'),
            'ACTIVE' => (new ORM\Fields\BooleanField('UF_ACTIVE'))->configureTitle('Активность'),
            'SYSTEM_ID' => (new ORM\Fields\IntegerField('UF_SYSTEM_ID'))->configureRequired()->configureTitle('Система'),
            'SYSTEM' => (new ORM\Fields\Relations\Reference('SYSTEM', \Api\Sensors\System\Table::getEntity(), ORM\Query\Query::filter()->whereColumn('this.SYSTEM_ID', '=', 'ref.ID')))->configureJoinType('inner'),
            'SENSOR_APP' => (new ORM\Fields\StringField('UF_SENSOR_APP'))->configureTitle('Приложение'),
            'SENSOR_DEVICE' => (new ORM\Fields\StringField('UF_SENSOR_DEVICE'))->configureTitle('Устройство'),
            'SENSOR_NAME' => (new ORM\Fields\StringField('UF_SENSOR_NAME'))->configureRequired()->configureTitle('Сенсор'),
            'SENSOR_UNIT' => (new ORM\Fields\StringField('UF_SENSOR_UNIT'))->configureTitle('Единицы измерений'),
            'ALERT_VALUE_MIN' => (new ORM\Fields\FloatField('UF_ALERT_VALUE_MIN'))->configureTitle('Минимальное допустимое значение'),
            'ALERT_VALUE_MAX' => (new ORM\Fields\FloatField('UF_ALERT_VALUE_MAX'))->configureTitle('Максимальное допустимое значение'),
            'VISUAL_MIN' => (new ORM\Fields\FloatField('UF_VISUAL_MIN'))->configureTitle('Минимум на графике'),
            'VISUAL_MAX' => (new ORM\Fields\FloatField('UF_VISUAL_MAX'))->configureTitle('Максимум на графике'),
            'ALERT_ENABLE' => (new ORM\Fields\BooleanField('UF_ALERT_ENABLE'))->configureTitle('Включить оповещение'),
            'ALERT_MUTE_TILL' => (new ORM\Fields\DateTimeField('UF_ALERT_MUTE_TILL'))->configureTitle('Отключить уведомления до'),
            'IGNORE_LESS' => (new ORM\Fields\FloatField('UF_IGNORE_LESS'))->configureTitle('Игнорировать значения меньше'),
            'IGNORE_MORE' => (new ORM\Fields\FloatField('UF_IGNORE_MORE'))->configureTitle('Игнорировать значения больше'),
            'LOG_MODE' => (new ORM\Fields\IntegerField('UF_LOG_MODE', array('values' => array(self::MODE_AVG, self::MODE_EACH, self::MODE_EACH_LAST_DAY))))->configureTitle('Режим логирования')->configureRequired(),
            'MODIFIER' => (new ORM\Fields\StringField('UF_MODIFIER'))->configureTitle('Формула для модификации значения'),
            'PRECISION' => (new ORM\Fields\IntegerField('UF_PRECISION'))->configureTitle('Количество знаков после запятой'),
            'SORT' => (new ORM\Fields\IntegerField('UF_SORT'))->configureTitle('Сортировка'),
            'LABEL' => (new ORM\Fields\StringField('UF_LABEL'))->configureTitle('Свое название'),
        );
    }

}
