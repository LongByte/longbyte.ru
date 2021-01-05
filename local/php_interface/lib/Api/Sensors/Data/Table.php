<?php

namespace Api\Sensors\Data;

use Bitrix\Main\ORM;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\Data\Table
 */
class Table extends ORM\Data\DataManager {

    /**
     *
     * @return string
     */
    public static function getTableName(): string {
        return 'sensors_data';
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
            'ID' => (new ORM\Fields\IntegerField('ID'))->configureTitle('ID')->configurePrimary()->configureAutocomplete(),
            'SENSOR_ID' => (new ORM\Fields\IntegerField('UF_SENSOR_ID'))->configureTitle('Сенсор')->configureRequired(),
            'SENSOR' => (new ORM\Fields\Relations\Reference('SENSOR', \Api\Sensors\Sensor\Table::getEntity(), ORM\Query\Query::filter()->whereColumn('this.SENSOR_ID', '=', 'ref.ID')))->configureJoinType('left'),
            'DATE' => (new ORM\Fields\DatetimeField('UF_DATE'))->configureTitle('Дата')->configureRequired(),
            'VALUE_MIN' => (new ORM\Fields\FloatField('UF_VALUE_MIN'))->configureTitle('Минимальное значение'),
            'VALUE_AVG' => (new ORM\Fields\FloatField('UF_VALUE_AVG'))->configureTitle('Значение'),
            'VALUE_MAX' => (new ORM\Fields\FloatField('UF_VALUE_MAX'))->configureTitle('Максимальное значение'),
            'VALUES_COUNT' => (new ORM\Fields\IntegerField('UF_VALUES_COUNT'))->configureTitle('Количество значений'),
            'VALUE' => (new ORM\Fields\FloatField('UF_VALUE'))->configureTitle('Текущее/последнее значение'),
        );
    }

}
