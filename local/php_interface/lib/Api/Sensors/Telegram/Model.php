<?php

namespace Api\Sensors\Telegram;

/**
 * Class \Api\Sensors\Telegram\Model
 */
class Model extends \Api\Core\Base\Model
{

    private static $token = '2013608355:AAFQx9HDR5GcOW-BBmELTn6XjtOIfYhajpU';

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

    /**
     * @return string
     */
    public static function getToken(): string
    {
        return self::$token;
    }

}
