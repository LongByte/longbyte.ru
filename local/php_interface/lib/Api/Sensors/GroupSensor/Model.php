<?php

namespace Api\Sensors\GroupSensor;

/**
 * Class \Api\Sensors\GroupSensor\Model
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
