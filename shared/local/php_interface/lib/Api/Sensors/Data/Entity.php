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
 * @method float getSensorValueMin()
 * @method $this setSensorValueMin(float $fSensorValueMin)
 * @method bool hasSensorValueMin()
 * @method float getSensorValue()
 * @method $this setSensorValue(float $fSensorValue)
 * @method bool hasSensorValue()
 * @method float getSensorValueMax()
 * @method $this setSensorValueMax(float $fSensorValueMax)
 * @method bool hasSensorValueMax()
 * @method int getSensorValues()
 * @method $this setSensorValues(int $iSensorValues)
 * @method bool hasSensorValues()
 */
class Entity extends \Api\Core\Base\Entity {

    /**
     *
     * @var \Api\Sensors\Sensor\Entity 
     */
    protected $_obSensor = null;

    /**
     *
     * @var float
     */
    protected $_lastValue = 0;

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

    public function toArray() {
        $arData = parent::toArray();
        $obSystem = $this->getSensor()->getSystem();
        if ($obSystem->isModeAvg()) {
            $strDate = $this->getDate()->format('d.m.Y');
        }
        if ($obSystem->isModeEach()) {
            $strDate = $this->getDate()->format('H:i:s');
        }
        $arData['date'] = $strDate;
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

    /**
     * 
     * @return float
     */
    public function getLastValue() {
        return $this->_lastValue;
    }

    /**
     * 
     * @param float $fLastValue
     * @return $this
     */
    public function setLastValue(float $fLastValue) {
        $this->_lastValue = $fLastValue;
        return $this;
    }

}
