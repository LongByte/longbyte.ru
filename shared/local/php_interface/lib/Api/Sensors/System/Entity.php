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
 * @method string getEmail()
 * @method $this setEmail(string $strEmail)
 * @method bool hasEmail()
 * @method \Bitrix\Main\Type\DateTime getLastUpdate()
 * @method $this setLastUpdate(\Bitrix\Main\Type\DateTime $obLastUpdate)
 * @method bool hasLastUpdate()
 * @method \Bitrix\Main\Type\DateTime getLastReceive()
 * @method $this setLastReceive(\Bitrix\Main\Type\DateTime $obLastReceive)
 * @method bool hasLastReceive()
 * @method int getUserId()
 * @method $this setUserId(int $iUserId)
 * @method bool hasUserId()
 */
class Entity extends \Api\Core\Base\Entity {

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
     * @return array
     */
    public function toArray() {
        $arData = parent::toArray();
        $arData['last_update'] = $this->getLastUpdate()->format('d.m.Y H:i:s');
        $arData['last_receive'] = $this->getLastReceive()->format('d.m.Y H:i:s');
        return $arData;
    }

}
