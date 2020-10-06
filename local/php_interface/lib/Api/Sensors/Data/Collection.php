<?php

namespace Api\Sensors\Data;

/**
 * Class \Api\Sensors\Data\Collection
 */
class Collection extends \Api\Core\Base\Collection {

    /**
     *
     * @var \Bitrix\Main\Type\Date
     */
    protected $obDate = null;

    /**
     * 
     * @param int $iSensorId
     * @return \Api\Sensors\Data\Entity|null
     */
    public function getBySensorId(int $iSensorId): ?\Api\Sensors\Data\Entity {
        /** @var \Api\Sensors\Data\Entity $obValue */
        foreach (array_reverse($this->getCollection()) as $obValue) {
            if ($obValue->getSensorId() == $iSensorId) {
                return $obValue;
            }
        }
        return null;
    }

    /**
     * 
     * @param \Bitrix\Main\Type\DateTime $obDateTime
     * @param int $iSensorId
     * @return \Api\Sensors\Data\Entity|null
     */
    public function getByDateAndSensorId(\Bitrix\Main\Type\DateTime $obDateTime, int $iSensorId): ?\Api\Sensors\Data\Entity {
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

    /**
     * 
     * @return \Bitrix\Main\Type\Date|null
     */
    public function getDate(): ?\Bitrix\Main\Type\Date {
        return $this->obDate;
    }

    /**
     * 
     * @param \Bitrix\Main\Type\Date $obDate
     * @return $this
     */
    public function setDate(\Bitrix\Main\Type\Date $obDate): self {
        $this->obDate = $obDate;
        return $this;
    }

    /**
     * 
     * @param array $arErrors
     * @return $this
     */
    public function save(array &$arErrors): self {
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
