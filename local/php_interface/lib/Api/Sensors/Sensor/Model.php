<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Model
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
    
    /**
     * 
     * @param string $string
     * @return string
     */
    public static function normalize($string): string {
        return str_replace(' ', '', $string);
    }

}
