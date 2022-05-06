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
class Entity extends \Api\Core\Base\Entity
{

    protected ?\Api\Sensors\Sensor\Collection $_obSensorsCollection = null;

    public function getFields(): array
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

    public function getSensorsCollection(): \Api\Sensors\Sensor\Collection
    {
        if (is_null($this->_obSensorsCollection)) {
            $this->_obSensorsCollection = new \Api\Sensors\Sensor\Collection();
        }
        return $this->_obSensorsCollection;
    }

    public function setSensorsCollection(\Api\Sensors\Sensor\Collection $obSensorsCollection): self
    {
        $this->_obSensorsCollection = $obSensorsCollection;
        return $this;
    }

    public function getNameToken(): string
    {
        return $this->getName() . '-' . $this->getToken();
    }

    public function toArray(): array
    {
        $arData = parent::toArray();
        if (!is_null($this->getLastUpdate())) {
            $arData['last_update'] = $this->getLastUpdate()->format('d.m.Y H:i:s');
        }
        if (!is_null($this->getLastReceive())) {
            $arData['last_receive'] = $this->getLastReceive()->format('d.m.Y H:i:s');
        }
        return $arData;
    }

}
