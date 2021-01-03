<?php

namespace Api\Sensors\GroupSensor;

use Bitrix\Main\ORM;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\GroupSensor\Table
 */
class Table extends ORM\Data\DataManager {

    /**
     *
     * @return string
     */
    public static function getTableName(): string {
        return 'sensors_group_sensor';
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
    public static function getMap(): array {
        return array(
            'ID' => (new ORM\Fields\IntegerField('ID'))
                ->configureTitle('ID')
                ->configureAutocomplete()
                ->configurePrimary(),
            'GROUP_ID' => (new ORM\Fields\IntegerField('UF_GROUP_ID'))
                ->configureTitle('Группа')
                ->configureRequired(),
            'GROUP' => (new ORM\Fields\Relations\Reference(
                    'GROUP',
                    \Api\Sensors\Group\Table::getEntity(),
                    ORM\Query\Query::filter()->whereColumn('this.GROUP_ID', '=', 'ref.ID'))
                )
                ->configureJoinType('left'),
            'SENSOR_ID' => (new ORM\Fields\IntegerField('UF_SENSOR_ID'))
                ->configureTitle('Серсор')
                ->configureRequired(),
            'SENSOR' => (new ORM\Fields\Relations\Reference(
                    'SENSOR',
                    \Api\Sensors\Sensor\Table::getEntity(),
                    ORM\Query\Query::filter()->whereColumn('this.SENSOR_ID', '=', 'ref.ID'))
                )
                ->configureJoinType('left'),
        );
    }

}
