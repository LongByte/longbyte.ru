<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Entity
 *
 * @method int getId()
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
 * @method int getPrecision()
 * @method $this setPrecision(int $iPrecision)
 * @method bool hasPrecision()
 * @method int getSort()
 * @method $this setSort(int $iSort)
 * @method bool hasSort()
 * @method string getLabel()
 * @method $this setLabel(string $strLabel)
 * @method bool hasLabel()
 */
class Entity extends \Api\Core\Base\Entity
{

    protected ?\Api\Sensors\System\Entity $_obSystem = null;
    protected ?\Api\Sensors\Data\Collection $_obValuesCollection = null;
    protected ?\Api\Sensors\Alert\Entity $_obAlert = null;
    protected bool $_bToday = false;
    protected bool $_bNew = false;
    protected ?\Api\Sensors\Sensor\Statistic\Entity $_obStatistic = null;

    public static function getFields(): array
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

    public function isModeAvg(): bool
    {
        return $this->getLogMode() == Table::MODE_AVG;
    }

    public function isModeEach(): bool
    {
        return $this->getLogMode() == Table::MODE_EACH;
    }

    public function isModeEachLastDay(): bool
    {
        return $this->getLogMode() == Table::MODE_EACH_LAST_DAY;
    }

    public function isBooleanSensor(): bool
    {
        return $this->getSensorUnit() == 'Yes/No';
    }

    public function isAllowAlert(): bool
    {
        if (!$this->getAlertEnable()) {
            return false;
        }
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

    public function toArray($arData = null): array
    {
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

        if (!is_null($this->_obStatistic)) {
            $arData['statistic'] = $this->_obStatistic->toArray();
        }

        return $arData;
    }

    public function getAlert(): \Api\Sensors\Alert\Entity
    {
        if (is_null($this->_obAlert)) {
            $this->_obAlert = new \Api\Sensors\Alert\Entity(array(
                'SENSOR_ID' => $this->getId(),
                'ALERT' => false,
                'DIRECTION' => 0,
                'VALUE_MIN' => 0,
                'VALUE_MAX' => 0,
            ));
            $this->_obAlert->setSensor($this);
        }
        return $this->_obAlert;
    }

    public function setAlert(\Api\Sensors\Alert\Entity $obAlert): self
    {
        $this->_obAlert = $obAlert;
        $this->_obAlert->setSensor($this);
        return $this;
    }

    public function hasAlert(): bool
    {
        return !is_null($this->_obAlert);
    }

    public function getValuesCollection(): \Api\Sensors\Data\Collection
    {
        if (is_null($this->_obValuesCollection)) {
            $this->_obValuesCollection = new \Api\Sensors\Data\Collection();
        }
        return $this->_obValuesCollection;
    }

    public function getSystem(): \Api\Sensors\System\Entity
    {
        if (is_null($this->_obSystem)) {
            $this->_obSystem = new \Api\Sensors\System\Entity($this->getSystemId());
        }
        return $this->_obSystem;
    }

    public function setSystem(\Api\Sensors\System\Entity $obSystem): self
    {
        $this->_obSystem = $obSystem;
        $this->setSystemId($obSystem->getId());
        return $this;
    }

    public function isToday(): bool
    {
        return $this->_bToday;
    }

    public function setToday(bool $bToday = true): self
    {
        $this->_bToday = $bToday;
        return $this;
    }

    public function isNew(): bool
    {
        return $this->_bNew;
    }

    public function setNew(bool $bNew = true): self
    {
        $this->_bNew = $bNew;
        return $this;
    }

    public function setStatistic(\Api\Sensors\Sensor\Statistic\Entity $obStatistic): self
    {
        $this->_obStatistic = $obStatistic;
        return $this;
    }

}
