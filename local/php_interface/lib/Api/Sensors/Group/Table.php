<?php

namespace Api\Sensors\Group;

use Bitrix\Main\ORM;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\Group\Table
 */
class Table extends ORM\Data\DataManager
{

    public static function getTableName(): string
    {
        return 'sensors_group';
    }

    public static function getScalarFields(): array
    {
        $arFields = array();
        foreach (self::getMap() as $strId => $obField) {
            if ($obField instanceof ORM\Fields\ScalarField) {
                $arFields[$strId] = $obField;
            }
        }
        return $arFields;
    }

    public static function getMap(): array
    {
        return array(
            'ID' => (new ORM\Fields\IntegerField('ID'))
                ->configureTitle('ID')
                ->configureAutocomplete()
                ->configurePrimary(),
            'SYSTEM_ID' => (new ORM\Fields\IntegerField('UF_SYSTEM_ID'))
                ->configureTitle('Система')
                ->configureRequired(),
            'SYSTEM' => (new ORM\Fields\Relations\Reference(
                'SYSTEM',
                \Api\Sensors\System\Table::getEntity(),
                ORM\Query\Query::filter()->whereColumn('this.USER_ID', '=', 'ref.ID'))
            )
                ->configureJoinType('left'),
            'SORT' => (new ORM\Fields\StringField('UF_SORT'))
                ->configureRequired()
                ->configureTitle('Сортировка'),
            'NAME' => (new ORM\Fields\StringField('UF_NAME'))
                ->configureRequired()
                ->configureTitle('Название'),
        );
    }

}
