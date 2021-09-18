<?php

namespace Api\Sensors\Telegram;

/**
 * Class \Api\Sensors\Telegram\Entity
 *

 */
class Entity extends \Api\Core\Base\Entity
{


    /**
     *
     * @return array
     */
    public function getFields(): array
    {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    /**
     *
     * @return string
     */
    public static function getCollection(): string
    {
        return Collection::class;
    }

    /**
     *
     * @return string
     */
    public static function getModel(): string
    {
        return Model::class;
    }


    /**
     *
     * @return array
     */
    public function toArray(): array
    {
        $arData = parent::toArray();

        return $arData;
    }

}
