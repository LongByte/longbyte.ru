<?php

namespace Api\Sensors\Telegram;

/**
 * Class \Api\Sensors\Telegram\Model
 */
class Model extends \Api\Core\Base\Model
{

    private static string $token = '2013608355:AAFQx9HDR5GcOW-BBmELTn6XjtOIfYhajpU';

    public static function getTable(): string
    {
        return Table::class;
    }

    public static function getEntity(): string
    {
        return Entity::class;
    }

    public static function getToken(): string
    {
        return self::$token;
    }

}
