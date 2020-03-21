<?php

namespace Api\Sensors\Data;

/**
 * Class \Api\Sensors\Data\Model
 */
class Model extends \Api\Core\Model\Base {

    public static function getTable() {
        return \Api\Sensors\Data\Table::class;
    }

    public static function getEntity() {
        return \Api\Sensors\Data\Entity::class;
    }

    public static function getCollection() {
        return \Api\Sensors\Data\Collection::class;
    }

}
