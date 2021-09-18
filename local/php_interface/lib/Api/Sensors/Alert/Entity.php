<?php

namespace Api\Sensors\Alert;

/**
 * Class \Api\Sensors\Alert\Entity
 *
 * @method int getSensorId()
 * @method $this setSensorId(int $iSensorId)
 * @method bool getAlert()
 * @method $this setAlert(bool $bAlert)
 * @method int getDirection()
 * @method $this setDirection(int $iDirection)
 * @method float getValueMin()
 * @method $this setValueMin(float $fValueMin)
 * @method float getValueMax()
 * @method $this setValueMax(float $fValueMax)
 */
class Entity extends \Api\Core\Base\Virtual\Entity
{

    protected static $_primaryField = 'SENSOR_ID';

    /** @var \Api\Sensors\Sensor\Entity|null */
    protected $obSensor = null;

    /**
     *
     * @var array
     */
    protected static $arFields = array(
        'SENSOR_ID',
        'ALERT',
        'DIRECTION',
        'VALUE_MIN',
        'VALUE_MAX'
    );

    /**
     *
     * @return string
     */
    public static function getCollection(): string
    {
        return Collection::class;
    }

    /**
     *
     * @return string
     */
    public static function getModel(): string
    {
        return Model::class;
    }

    /**
     *
     * @return bool
     */
    public function isAlert(): bool
    {
        return $this->getAlert() == true;
    }

    /**
     * @return $this
     */
    public function setTooHigh(): self
    {
        $this->setDirection(1);
        return $this;
    }

    /**
     * @return $this
     */
    public function setTooLow(): self
    {
        $this->setDirection(-1);
        return $this;
    }

    /**
     * @return bool
     */
    public function isTooHigh(): bool
    {
        return $this->getDirection() == 1;
    }

    /**
     * @return bool
     */
    public function isTooLow(): bool
    {
        return $this->getDirection() == -1;
    }

    /**
     * @return \Api\Sensors\Sensor\Entity|null
     */
    public function getSensor(): ?\Api\Sensors\Sensor\Entity
    {
        return $this->obSensor;
    }

    /**
     * @param \Api\Sensors\Sensor\Entity $obSensor
     * @return $this
     */
    public function setSensor(\Api\Sensors\Sensor\Entity $obSensor): self
    {
        if (is_null($this->obSensor)) {
            $this->obSensor = $obSensor;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getEmailMessage(): string
    {
        $strMessage = '';
        if (!is_null($this->getSensor())) {
            $strMessage .= 'Значение на датчике ' . $this->getSensor()->getSensorApp() . ' > ' . $this->getSensor()->getSensorDevice() . ' > ' . $this->getSensor()->getSensorName() . ' = ';
            if ($this->isTooLow()) {
                $strMessage .= $this->getSensor()->getAlert()->getValueMin() . $this->getSensor()->getSensorUnit() . ' и меньше допустимого ' . $this->getSensor()->getAlertValueMin();
            }
            if ($this->isTooHigh()) {
                $strMessage .= $this->getSensor()->getAlert()->getValueMax() . $this->getSensor()->getSensorUnit() . ' и больше допустимого ' . $this->getSensor()->getAlertValueMax();
            }
            $strMessage .= $this->getSensor()->getSensorUnit();
        }
        return $strMessage;
    }

    /**
     * @return string
     */
    private function getDirectionSymbol(): string
    {
        $tooLow = '⬇';
        $toHigh = '⬆';
        if ($this->isTooLow()) {
            return $tooLow;
        }
        if ($this->isTooHigh()) {
            return $toHigh;
        }
        return '';
    }

    /**
     * @return string
     */
    public function getTelegramMessage(): string
    {
        $strMessage = '';
        if (!is_null($this->getSensor())) {

            switch ($this->getSensor()->getSensorUnit()) {
                case 'MB':
                case 'GB':
                    $strMessage .= '📊' . $this->getDirectionSymbol();
                    break;

                case '%':
                    $strMessage .= '⚠' . $this->getDirectionSymbol();
                    break;

                case '°C':
                    $strMessage .= '🌡';
                    if ($this->isTooLow()) {
                        $strMessage .= '❄';
                    }
                    if ($this->isTooHigh()) {
                        $strMessage .= '🔥';
                    }
                    break;

                case 'W':
                case 'A':
                case 'V':
                    $strMessage .= '⚡' . $this->getDirectionSymbol();
                    break;

                case 'Yes/No':
                    $strMessage .= '🛠';
                    break;

                case 'RPM':
                    $strMessage .= '🌪' . $this->getDirectionSymbol();
                    break;
            }

            $strMessage .= $this->getSensor()->getSensorDevice() . ' > ' . $this->getSensor()->getSensorName() . ' = ';
            if ($this->isTooLow()) {
                $strMessage .= '<b>' . $this->getSensor()->getAlert()->getValueMin() . $this->getSensor()->getSensorUnit() . '</b> и меньше допустимого ' . $this->getSensor()->getAlertValueMin();
            }
            if ($this->isTooHigh()) {
                $strMessage .= '<b>' . $this->getSensor()->getAlert()->getValueMax() . $this->getSensor()->getSensorUnit() . '</b> и больше допустимого ' . $this->getSensor()->getAlertValueMax();
            }
            $strMessage .= $this->getSensor()->getSensorUnit();

        }
        return $strMessage;
    }

}
