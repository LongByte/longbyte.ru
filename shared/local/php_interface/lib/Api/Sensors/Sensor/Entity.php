<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Entity
 * 
 * @method int getId()
 * @method $this setId(int $iId)
 * @method bool hasId()
 * @method boolean getActive()
 * @method $this setActive(boolean $bActive)
 * @method bool hasActive()
 * @method int getSystemId()
 * @method $this setSystemId(int $iSystemId)
 * @method bool hasSystemId()
 * @method string getSensorApp()
 * @method $this setSensorApp(string $strSensorApp)
 * @method bool hasSensorApp()
 * @method string getSensorDevice()
 * @method $this setSensorDevice(string $strSensorDevice)
 * @method bool hasSensorDevice()
 * @method string getSensorName()
 * @method $this setSensorName(string $strSensorName)
 * @method bool hasSensorName()
 * @method string getSensorUnit()
 * @method $this setSensorUnit(string $strSensorUnit)
 * @method bool hasSensorUnit()
 * @method float getAlertValueMin()
 * @method $this setAlertValueMin(float $fAlertValueMin)
 * @method bool hasAlertValueMin()
 * @method float getAlertValueMax()
 * @method $this setAlertValueMax(float $fAlertValueMax)
 * @method bool hasAlertValueMax()
 */
class Entity extends \Api\Core\Entity\Base {

    protected $_alert = false;
    protected $_alertDirection = 0;

    /**
     *
     * @var \Api\Sensors\Data\Collection
     */
    protected $_valuesCollection = null;

    /**
     * 
     * @return array
     */
    public function getFields() {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    /**
     * 
     * @return \Api\Sensors\Sensor\Model
     */
    protected static function getModel() {
        return \Api\Sensors\Sensor\Model::class;
    }

    /**
     * 
     * @return bool
     */
    public function isAlert() {
        return $this->_alert;
    }

    /**
     * 
     * @param bool $bAlert
     * @return $this
     */
    public function setAlert(bool $bAlert = true) {
        $this->_alert = $bAlert;
        return $this;
    }

    /**
     * 
     * @param int $iDirection
     * @return $this
     */
    public function setAlertDirection(int $iDirection) {
        $this->_alertDirection = $iDirection;
        return $this;
    }

    /**
     * 
     * @param \Api\Sensors\Data\Entity $obValue
     * @return $this
     */
    public function addValue(\Api\Sensors\Data\Entity $obValue) {
        if (is_null($this->_valuesCollection)) {
            $this->_valuesCollection = new \Api\Sensors\Data\Collection();
        }
        $this->_valuesCollection->addItem($obValue);
        return $this;
    }

}
