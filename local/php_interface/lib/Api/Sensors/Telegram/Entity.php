<?php

namespace Api\Sensors\Telegram;

/**
 * Class \Api\Sensors\Telegram\Entity
 *
 * @method int getId()
 * @method boolean getActive()
 * @method $this setActive(boolean $bActive)
 * @method int getSystemId()
 * @method $this setSystemId(int $iSystemId)
 * @method string getChatId()
 * @method $this setChatId(string $strChatId)
 */
class Entity extends \Api\Core\Base\Entity
{

    public static function getFields(): array
    {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    public static function getCollection(): string
    {
        return Collection::class;
    }

    public static function getModel(): string
    {
        return Model::class;
    }

    public function toArray($arData = null): array
    {
        $arData = parent::toArray();

        return $arData;
    }

}
