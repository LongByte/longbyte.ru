<?php

namespace Api\Sensors\Sensor\Statistic;

/**
 * Class \Api\Sensors\Sensor\Statistic\Model
 */
class Model extends \Api\Core\Base\Virtual\Model
{

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
