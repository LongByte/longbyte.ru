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
     * @return array
     */
    public function getFields() {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    protected static function getModel() {
        return \Api\Sensors\System\Model::class;
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
