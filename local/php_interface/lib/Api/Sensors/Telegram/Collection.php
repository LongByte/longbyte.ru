<?php

namespace Api\Sensors\Telegram;

/**
 * Class \Api\Sensors\Telegram\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    protected $arSystemIds = array();

    public function addItem(Entity $obEntity)
    {
        parent::addItem($obEntity);
        $this->arSystemIds[] = $obEntity->getSystemId();
        return $this;
    }

    /**
     * @return array
     */
    public function getSystemIds(): array
    {
        return $this->arSystemIds;
    }

}
