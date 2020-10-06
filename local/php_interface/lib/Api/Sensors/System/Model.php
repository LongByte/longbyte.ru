<?php

namespace Api\Sensors\System;

/**
 * Class \Api\Sensors\System\Model
 */
class Model extends \Api\Core\Base\Model {

    public static function getTable(): string {
        return Table::class;
    }

    public static function getEntity(): string {
        return Entity::class;
    }

}
