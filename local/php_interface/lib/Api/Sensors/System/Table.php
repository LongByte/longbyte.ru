<?php

namespace Api\Sensors\System;

use Bitrix\Main\ORM;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\System\Table
 */
class Table extends ORM\Data\DataManager
{

    public static function getTableName(): string
    {
        return 'sensors_system';
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

    public static function getMap()
    {
        return array(
            'ID' => (new ORM\Fields\IntegerField('ID'))->configureTitle('ID')->configureAutocomplete()->configurePrimary(),
            'ACTIVE' => (new ORM\Fields\BooleanField('UF_ACTIVE'))->configureTitle('Активность')->configureRequired(),
            'NAME' => (new ORM\Fields\StringField('UF_NAME'))->configureRequired()->configureTitle('Название'),
            'TOKEN' => (new ORM\Fields\StringField('UF_TOKEN'))->configureRequired()->configureTitle('Токен'),
            'EMAIL' => (new ORM\Fields\StringField('UF_EMAIL'))->configureRequired()->configureTitle('E-mail для уведомлений'),
            'LAST_UPDATE' => (new ORM\Fields\DateTimeField('UF_LAST_UPDATE'))->configureRequired()->configureTitle('Последнее обновление данных'),
            'LAST_RECEIVE' => (new ORM\Fields\DateTimeField('UF_LAST_RECEIVE'))->configureRequired()->configureTitle('Последнее получение данных'),
            'USER_ID' => (new ORM\Fields\IntegerField('UF_USER_ID'))->configureRequired()->configureTitle('Пользователь'),
            'USER' => new ORM\Fields\Relations\Reference('USER', \Bitrix\Main\UserTable::getEntity(), array('this.USER_ID' => 'ref.ID'), array('join_type' => 'LEFT')
            ),
        );
    }

}
