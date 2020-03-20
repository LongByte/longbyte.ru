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
class Entity extends \Api\Core\Entity\Base {

    protected $_systemMode = 0;

    /**
     * 
     * @return array
     */
    public function getFields() {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    /**
     * 
     * @return \Api\Sensors\Data\Model
     */
    protected static function getModel() {
        return \Api\Sensors\Data\Model::class;
    }

    /**
     * 
     * @param int $iSystemMode
     * @return $this
     */
    public function setSystemMode(int $iSystemMode) {
        $this->_systemMode = $iSystemMode;
        return $this;
    }

}
