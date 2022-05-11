<?php

namespace Api\Sensors\GroupSensor;

/**
 * Class \Api\Sensors\GroupSensor\Entity
 *
 * @method int getId()
 * @method int getGroupId()
 * @method $this setGroupId(int $iGroupId)
 * @method bool hasGroupId()
 * @method int getSensorId()
 * @method $this setSensorId(int $iSensorId)
 * @method bool hasSensorId()
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

    public function toArray(): array
    {
        $arData = parent::toArray();
        return $arData;
    }

}
