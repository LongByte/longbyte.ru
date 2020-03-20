<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Model
 */
class Model extends \Api\Core\Model\Base {

    protected static function getTable() {
        return \Api\Sensors\Sensor\Table::class;
    }

    protected static function getEntity() {
        return \Api\Sensors\Sensor\Entity::class;
    }

    protected static function getCollection() {
        return \Api\Sensors\Sensor\Collection::class;
    }

}
