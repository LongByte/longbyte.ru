<?php

namespace Api\Sensors\Telegram;

/**
 * Class \Api\Sensors\Telegram\Model
 */
class Model extends \Api\Core\Base\Model
{

    /**
     *
     * @return string
     */
    public static function getTable(): string
    {
        return Table::class;
    }

    /**
     *
     * @return string
     */
    public static function getEntity(): string
    {
        return Entity::class;
    }

}
