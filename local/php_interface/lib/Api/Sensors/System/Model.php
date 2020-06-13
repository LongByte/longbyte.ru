<?php

namespace Api\Sensors\System;

/**
 * Class \Api\Sensors\System\Model
 */
class Model extends \Api\Core\Base\Model {

    public static function getTable() {
        return Table::class;
    }

    public static function getEntity() {
        return Entity::class;
    }

}
