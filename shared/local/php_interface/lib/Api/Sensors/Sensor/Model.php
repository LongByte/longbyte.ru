<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Model
 */
class Model extends \Api\Core\Model\Base {

    public static function getTable() {
        return \Api\Sensors\Sensor\Table::class;
    }

    public static function getEntity() {
        return \Api\Sensors\Sensor\Entity::class;
    }

    public static function getCollection() {
        return \Api\Sensors\Sensor\Collection::class;
    }

}
