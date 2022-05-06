<?php

namespace Api\Sensors\Data;

/**
 * Class \Api\Sensors\Data\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    protected ?\Bitrix\Main\Type\Date $obDate = null;

    public function getBySensorId(int $iSensorId): ?\Api\Sensors\Data\Entity
    {
        /** @var \Api\Sensors\Data\Entity $obValue */
        foreach (array_reverse($this->getCollection()) as $obValue) {
            if ($obValue->getSensorId() == $iSensorId) {
                return $obValue;
            }
        }
        return null;
    }

    public function getByDateAndSensorId(\Bitrix\Main\Type\DateTime $obDateTime, int $iSensorId): ?\Api\Sensors\Data\Entity
    {
        $obDateTime = clone $obDateTime;
        $obDateTime->setTime(0, 0, 0);
        /** @var \Api\Sensors\Data\Entity $obValue */
        foreach ($this->getCollection() as $obValue) {
            $obValueDateTime = clone $obValue->getDate();
            $obValueDateTime->setTime(0, 0, 0);
            if ($obDateTime->getTimestamp() == $obValueDateTime->getTimestamp() && $obValue->getSensorId() == $iSensorId) {
                return $obValue;
            }
        }
        return null;
    }

    public function getDate(): ?\Bitrix\Main\Type\Date
    {
        return $this->obDate;
    }

    public function setDate(\Bitrix\Main\Type\Date $obDate): self
    {
        $this->obDate = $obDate;
        return $this;
    }

    public function save(array &$arErrors): self
    {
        /** @var \Api\Sensors\Data\Entity $obValue */
        foreach ($this->getCollection() as $obValue) {
            if ($obValue->isChanged()) {
                $obValue->save();
                if (!$obValue->isExists()) {
                    $arErrors[] = 'Невозможно добавить данные. Ошибка: ' . print_r($obValue->getDBResult()->getErrorMessages(), true) . '. Данные: ' . print_r($obValue->toArray(), true);
                }
            }
        }
        return $this;
    }

}
