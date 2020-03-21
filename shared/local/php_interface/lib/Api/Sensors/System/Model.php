<?php

namespace Api\Sensors\System;

/**
 * Class \Api\Sensors\System\Model
 */
class Model extends \Api\Core\Model\Base {

    public static function getTable() {
        return \Api\Sensors\System\Table::class;
    }

    public static function getEntity() {
        return \Api\Sensors\System\Entity::class;
    }

    public static function getCollection() {
        return \Api\Sensors\System\Collection::class;
    }

}
