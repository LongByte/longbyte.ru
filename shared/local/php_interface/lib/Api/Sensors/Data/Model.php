<?php

namespace Api\Sensors\Data;

/**
 * Class \Api\Sensors\Data\Model
 */
class Model extends \Api\Core\Model\Base {

    protected static function getTable() {
        return \Api\Sensors\Data\Table::class;
    }

    protected static function getEntity() {
        return \Api\Sensors\Data\Entity::class;
    }

    protected static function getCollection() {
        return \Api\Sensors\Data\Collection::class;
    }

}
