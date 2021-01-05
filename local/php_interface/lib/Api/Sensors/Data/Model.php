<?php

namespace Api\Sensors\Data;

/**
 * Class \Api\Sensors\Data\Model
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
