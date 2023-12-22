<?php

namespace Api\Sensors\Telegram;

/**
 * Class \Api\Sensors\Telegram\Model
 */
class Model extends \Api\Core\Base\Model
{

    private static string $token = '---';

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
