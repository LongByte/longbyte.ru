<?php

namespace Api\Sensors\GroupSensor;

/**
 * Class \Api\Sensors\GroupSensor\Model
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
