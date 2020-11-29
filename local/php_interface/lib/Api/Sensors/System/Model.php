<?php

namespace Api\Sensors\System;

/**
 * Class \Api\Sensors\System\Model
 */
class Model extends \Api\Core\Base\Model {

    /**
     * 
     * @return string
     */
    public static function getTable(): string {
        return Table::class;
    }

    /**
     * 
     * @return string
     */
    public static function getEntity(): string {
        return Entity::class;
    }

}
