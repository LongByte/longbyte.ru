<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Model
 */
class Model extends \Api\Core\Base\Model {

    public static function getTable(): string {
        return Table::class;
    }

    public static function getEntity(): string {
        return Entity::class;
    }

}
