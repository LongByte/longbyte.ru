<?php

namespace Api\Sensors\Device;

/**
 * Class \Api\Sensors\Device\Model
 */
class Model extends \Api\Core\Base\Virtual\Model
{

    public static function getEntity(): string
    {
        return Entity::class;
    }

}
