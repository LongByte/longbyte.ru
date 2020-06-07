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
class Entity extends \Api\Core\Base\Entity {

    /**
     *
     * @var \Api\Sensors\Sensor\Entity 
     */
    protected $_obSensor = null;

    /**
     * 
     * @return array
     */
    public function getFields() {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    /**
     * 
     * @return string
     */
    public static function getCollection() {
        return Collection::class;
    }

    /**
     * 
     * @return string
     */
    public static function getModel() {
        return Model::class;
    }

    /**
     * 
     * @return array
     */
    public function toArray() {
        $obSensor = $this->getSensor();

        $obNow = new \Bitrix\Main\Type\DateTime();
        $obNow->setTime(0, 0, 0);
        $obValueDate = clone $this->getDate();
        $obValueDate->setTime(0, 0, 0);
        $bToday = $obNow->getTimestamp() == $obValueDate->getTimestamp();

        if ($obSensor->isModeAvg() || $obSensor->isModeEachLastDay() && !$bToday) {
            $strDate = $this->getDate()->format('d.m.Y');
        }
        if ($obSensor->isModeEach() || $obSensor->isModeEachLastDay() && $bToday) {
            $strDate = $this->getDate()->format(\DateTime::ATOM);
        }

        $arData = array();
        $arData['date'] = $strDate;
        if ($this->getValue()) {
            $arData['value'] = $this->getValue();
        }

        $bBoolView = $obSensor->isBooleanSensor() && !$obSensor->isToday();

        if ($this->getValueMin() || $bBoolView) {
            $arData['value_min'] = $this->getValueMin();
        }
        if ($this->getValueAvg() || $bBoolView) {
            $arData['value_avg'] = $this->getValueAvg();
        }
        if ($this->getValueMax() || $bBoolView) {
            $arData['value_max'] = $this->getValueMax();
        }
        return $arData;
    }

    /**
     * 
     * @return \Api\Sensors\Sensor\Entity 
     */
    public function getSensor() {
        if (is_null($this->_obSensor)) {
            $this->_obSensor = new \Api\Sensors\Sensor\Entity($this->getSensorId());
        }
        return $this->_obSensor;
    }

    /**
     * 
     * @param \Api\Sensors\Sensor\Entity $obSensor
     * @return $this
     */
    public function setSensor(\Api\Sensors\Sensor\Entity $obSensor) {
        $this->_obSensor = $obSensor;
        $this->setSensorId($obSensor->getId());
        return $this;
    }

}
