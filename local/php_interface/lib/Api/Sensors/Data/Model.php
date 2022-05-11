<?php

namespace Api\Sensors\Data;

/**
 * Class \Api\Sensors\Data\Model
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

}
