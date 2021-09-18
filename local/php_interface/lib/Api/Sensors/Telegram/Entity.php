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
