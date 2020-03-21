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
class Entity extends \Api\Core\Base\Entity {

    protected $_alert = false;
    protected $_alertDirection = 0;

    /**
     *
     * @var \Api\Sensors\System\Entity 
     */
    protected $_obSystem = null;

    /**
     *
     * @var \Api\Sensors\Data\Collection
     */
    protected $_obValuesCollection = null;

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
        return \Api\Sensors\Sensor\Model::class;
    }

    public function toArray() {
        $arData = parent::toArray();
        $arData['values'] = $this->getValuesCollection()->toArray();
        return $arData;
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
     * @return \Api\Sensors\Data\Collection
     */
    public function getValuesCollection() {
        if (is_null($this->_obValuesCollection)) {
            $this->_obValuesCollection = new \Api\Sensors\Data\Collection();
        }
        return $this->_obValuesCollection;
    }

    /**
     * 
     * @return \Api\Sensors\System\Entity 
     */
    public function getSystem() {
        if (is_null($this->_obSystem)) {
            $this->_obSystem = new \Api\Sensors\System\Entity($this->getSensorId());
        }
        return $this->_obSystem;
    }

    /**
     * 
     * @param \Api\Sensors\System\Entity $obSystem
     * @return $this
     */
    public function setSystem(\Api\Sensors\System\Entity $obSystem) {
        $this->_obSystem = $obSystem;
        $this->setSystemId($obSystem->getId());
        return $this;
    }

}
