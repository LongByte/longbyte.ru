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
 * @method float getVisualMin()
 * @method $this setVisualMin(float $fVisualMin)
 * @method bool hasVisualMin()
 * @method float getVisualMax()
 * @method $this setVisualMax(float $fVisualMax)
 * @method bool hasVisualMax()
 * @method boolean getAlertEnable()
 * @method $this setAlertEnable(boolean $bAlertEnable)
 * @method bool hasAlertEnable()
 * @method \Bitrix\Main\Type\DateTime getAlertMuteTill()
 * @method $this setAlertMuteTill(\Bitrix\Main\Type\DateTime $obAlertMuteTill)
 * @method bool hasAlertMuteTill()
 * @method float getIgnoreLess()
 * @method $this setIgnoreLess(float $fIgnoreLess)
 * @method bool hasIgnoreLess()
 * @method float getIgnoreMore()
 * @method $this setIgnoreMore(float $fIgnoreMore)
 * @method bool hasIgnoreMore()
 * @method int getLogMode()
 * @method $this setLogMode(int $iLogMode)
 * @method bool hasLogMode()
 * @method string getModifier()
 * @method $this setModifier(string $strModifier)
 * @method bool hasModifier()
 * @method string getPrecision()
 * @method $this setPrecision(string $strPrecision)
 * @method bool hasPrecision()
 */
class Entity extends \Api\Core\Base\Entity {

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
     * @var \Api\Sensors\Alert\Entity
     */
    protected $_obAlert = null;

    /**
     *
     * @var bool
     */
    protected $_bToday = false;

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
     * @return bool
     */
    public function isModeAvg() {
        return $this->getLogMode() == Table::MODE_AVG;
    }

    /**
     * 
     * @return bool
     */
    public function isModeEach() {
        return $this->getLogMode() == Table::MODE_EACH;
    }

    /**
     * 
     * @return bool
     */
    public function isModeEachLastDay() {
        return $this->getLogMode() == Table::MODE_EACH_LAST_DAY;
    }

    /**
     * 
     * @return bool
     */
    public function isBooleanSensor() {
        return $this->getSensorUnit() == 'Yes/No';
    }

    /**
     * 
     * @return boolean
     */
    public function isAllowAlert() {
        if ($this->getAlertMuteTill() instanceof \Bitrix\Main\Type\DateTime) {
            $obNow = new \Bitrix\Main\Type\DateTime();
            if ($this->getAlertMuteTill()->getTimestamp() < $obNow->getTimestamp()) {
                $this->setAlertMuteTill(null);
                return true;
            }
            return false;
        }
        return true;
    }

    /**
     * 
     * @return array
     */
    public function toArray() {
        $arData = parent::toArray();
        $arData['alert'] = $this->getAlert()->toArray();
        $arData['values'] = $this->getValuesCollection()->toArray();
        if ($this->getAlertMuteTill() instanceof \Bitrix\Main\Type\DateTime) {
            $arData['alert_mute_till'] = $this->getAlertMuteTill()->format('d.m.Y H:i:s');
        }

        if ($this->isModeEach() || ($this->isModeEachLastDay() && $this->getValuesCollection()->count() > 1)) {
            $arData['view'] = 'line';
        } else {
            if ($this->isBooleanSensor()) {
                $arData['view'] = 'bool';
            } else {
                $arData['view'] = 'bar';
            }
        }
        return $arData;
    }

    /**
     * 
     * @return \Api\Sensors\Alert\Entity
     */
    public function getAlert() {
        if (is_null($this->_obAlert)) {
            $this->_obAlert = new \Api\Sensors\Alert\Entity(array(
                'SENSOR_ID' => $this->getId(),
                'ALERT' => false,
                'DIRECTION' => 0,
                'VALUE_MIN' => 0,
                'VALUE_MAX' => 0,
            ));
        }
        return $this->_obAlert;
    }

    /**
     * 
     * @param \Api\Sensors\Alert\Entity $obAlert
     * @return $this
     */
    public function setAlert(\Api\Sensors\Alert\Entity $obAlert) {
        $this->_obAlert = $obAlert;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function hasAlert() {
        return !is_null($this->_obAlert);
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

    /**
     * 
     * @return bool
     */
    public function isToday() {
        return $this->_bToday;
    }

    /**
     * 
     * @param bool $bToday
     * @return $this
     */
    public function setToday(bool $bToday = true) {
        $this->_bToday = $bToday;
        return $this;
    }

}
