<?php

namespace Api\Sensors\Telegram;

/**
 * Class \Api\Sensors\Telegram\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    protected array $arSystemIds = array();

    public function addItem(\Api\Core\Base\Entity $obEntity): self
    {
        parent::addItem($obEntity);
        $this->arSystemIds[] = $obEntity->getSystemId();
        return $this;
    }

    public function getSystemIds(): array
    {
        return $this->arSystemIds;
    }

}
