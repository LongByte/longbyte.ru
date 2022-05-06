<?php

namespace Api\Sensors\Telegram;

use Bitrix\Main\ORM;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

/**
 * Class \Api\Sensors\Telegram\Table
 */
class Table extends ORM\Data\DataManager
{

    public static function getTableName(): string
    {
        return 'sensors_telegram';
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
            'ACTIVE' => (new ORM\Fields\BooleanField('UF_ACTIVE'))->configureRequired()->configureTitle('Активность'),
            'SYSTEM_ID' => (new ORM\Fields\IntegerField('UF_SYSTEM_ID'))->configureRequired()->configureTitle('ID системы'),
            'CHAT_ID' => (new ORM\Fields\StringField('UF_CHAT_ID'))->configureRequired()->configureTitle('ID чата'),
        );
    }

}
