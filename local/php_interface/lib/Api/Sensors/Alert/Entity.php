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
class Entity extends \Api\Core\Base\Virtual\Entity {

    protected static $_primaryField = 'SENSOR_ID';

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
    public function isAlert() {
        return $this->getAlert() == true;
    }

}
