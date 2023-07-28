<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Model
 */
class Model extends \Api\Core\Base\Model
{

    public static function getTable(): string
    {
        return Table::class;
    }

    public static function getEntity(): string
    {
        return Entity::class;
    }

    public static function normalize(string $string): string
    {
        return str_replace(' ', '', $string);
    }

    public static function getAll(array $arFilter = array(), int $iLimit = 0, int $iOffset = 0, array $arParams = array()): Collection
    {
        return parent::getAll($arFilter, $iLimit, $iOffset, $arParams);
    }
}
