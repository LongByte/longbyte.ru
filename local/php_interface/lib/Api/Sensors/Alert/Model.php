<?php

namespace Api\Sensors\Alert;

/**
 * Class \Api\Sensors\Alert\Model
 */
class Model extends \Api\Core\Base\Virtual\Model {

    public static function getEntity(): string {
        return Entity::class;
    }

}
