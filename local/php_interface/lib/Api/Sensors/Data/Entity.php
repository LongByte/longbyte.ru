<?php

namespace Api\Sensors\Data;

/**
 * Class \Api\Sensors\Data\Entity
 *
 * @method int getId()
 * @method $this setId(int $iId)
 * @method bool hasId()
 * @method int getSensorId()
 * @method $this setSensorId(int $iSensorId)
 * @method bool hasSensorId()
 * @method \Bitrix\Main\Type\DateTime getDate()
 * @method $this setDate(\Bitrix\Main\Type\DateTime $obDate)
 * @method bool hasDate()
 * @method float getValueMin()
 * @method $this setValueMin(float $fValueMin)
 * @method bool hasValueMin()
 * @method float getValueAvg()
 * @method $this setValueAvg(float $fValueAvg)
 * @method bool hasValueAvg()
 * @method float getValueMax()
 * @method $this setValueMax(float $fValueMax)
 * @method bool hasValueMax()
 * @method int getValuesCount()
 * @method $this setValuesCount(int $iValuesCount)
 * @method bool hasValuesCount()
 * @method float getValue()
 * @method $this setValue(float $fValue)
 * @method bool hasValue()
 */
class Entity extends \Api\Core\Base\Entity
{

    protected ?\Api\Sensors\Sensor\Entity $_obSensor = null;

    public function getFields(): array
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

    public function toArray(): array
    {
        $obSensor = $this->getSensor();

        $obNow = new \Bitrix\Main\Type\DateTime();
        $obToday = (new \Bitrix\Main\Type\DateTime())->setTime(0, 0, 0);
        $obYesturday = (new \Bitrix\Main\Type\DateTime())->setTime(0, 0, 0)->add('-1day');
        $obValueDate = clone $this->getDate();
        $obValueDate->setTime(0, 0, 0);
        $bToday = $obToday->getTimestamp() == $obValueDate->getTimestamp();

        $bYesturday = $obYesturday->getTimestamp() == $obValueDate->getTimestamp() && $obNow->format('H') < 6;

        if ($obSensor->isModeAvg() || $obSensor->isModeEachLastDay() && !($bToday && $bYesturday)) {
            $strDate = $this->getDate()->format('d.m.Y');
        }
        if ($obSensor->isModeEach() || $obSensor->isModeEachLastDay() && ($bToday || $bYesturday)) {
            $strDate = $this->getDate()->format('H:i');
        }

        $arData = array();
        $arData['date'] = $strDate;
        if ($this->getValue()) {
            $arData['value'] = $this->getValue();
        }

        $arData['value_min'] = $this->getValueMin();
        $arData['value_avg'] = round($this->getValueAvg(), (int) $obSensor->getPrecision());
        $arData['value_max'] = $this->getValueMax();
        return $arData;
    }

    public function getSensor(): \Api\Sensors\Sensor\Entity
    {
        if (is_null($this->_obSensor)) {
            $this->_obSensor = new \Api\Sensors\Sensor\Entity($this->getSensorId());
        }
        return $this->_obSensor;
    }

    public function setSensor(\Api\Sensors\Sensor\Entity $obSensor): self
    {
        $this->_obSensor = $obSensor;
        $this->setSensorId($obSensor->getId());
        return $this;
    }

}
