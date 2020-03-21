<?php

namespace Api\Sensors\System;

/**
 * Class \Api\Sensors\System\Entity
 * 
 * @method int getId()
 * @method $this setId(int $iId)
 * @method bool hasId()
 * @method boolean getActive()
 * @method $this setActive(boolean $bActive)
 * @method bool hasActive()
 * @method string getName()
 * @method $this setName(string $strName)
 * @method bool hasName()
 * @method string getToken()
 * @method $this setToken(string $strToken)
 * @method bool hasToken()
 * @method int getMode()
 * @method $this setMode(int $iMode)
 * @method bool hasMode()
 * @method string getEmail()
 * @method $this setEmail(string $strEmail)
 * @method bool hasEmail()
 */
class Entity extends \Api\Core\Entity\Base {

    /**
     *
     * @var \Api\Sensors\Sensor\Collection
     */
    protected $_obSensorsCollection = null;

    /**
     * 
     * @return array
     */
    public function getFields() {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    public static function getModel() {
        return \Api\Sensors\System\Model::class;
    }

    /**
     * 
     * @return \Api\Sensors\Sensor\Collection
     */
    public function getSensorsCollection() {
        if (is_null($this->_obSensorsCollection)) {
            $this->_obSensorsCollection = new \Api\Sensors\Sensor\Collection();
        }
        return $this->_obSensorsCollection;
    }

    /**
     * 
     * @param \Api\Sensors\Sensor\Collection $obSensorsCollection
     * @return $this
     */
    public function setSensorsCollection(\Api\Sensors\Sensor\Collection $obSensorsCollection) {
        $this->_obSensorsCollection = $obSensorsCollection;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function isModeAvg() {
        return $this->getMode() == Table::MODE_AVG;
    }

    /**
     * 
     * @return bool
     */
    public function isModeEach() {
        return $this->getMode() == Table::MODE_EACH;
    }

}
