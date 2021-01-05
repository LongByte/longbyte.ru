<?php

namespace Api\Sensors\Group;

/**
 * Class \Api\Sensors\Group\Entity
 * 
 * @method int getId()
 * @method int getSystemId()
 * @method $this setSystemId(int $iSystemId)
 * @method bool hasSystemId()
 * @method string getSort()
 * @method $this setSort(string $strSort)
 * @method bool hasSort()
 * @method string getName()
 * @method $this setName(string $strName)
 * @method bool hasName()
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
    public function getFields(): array {
        return array_keys(static::getModel()::getTable()::getScalarFields());
    }

    /**
     * 
     * @return string
     */
    public static function getCollection(): string {
        return Collection::class;
    }

    /**
     * 
     * @return string
     */
    public static function getModel(): string {
        return Model::class;
    }

    /**
     * 
     * @return \Api\Sensors\Sensor\Collection
     */
    public function getSensorsCollection(): \Api\Sensors\Sensor\Collection {
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
    public function setSensorsCollection(\Api\Sensors\Sensor\Collection $obSensorsCollection): self {
        $this->_obSensorsCollection = $obSensorsCollection;
        return $this;
    }

    /**
     * 
     * @return array
     */
    public function toArray(): array {
        $arData = parent::toArray();
        return $arData;
    }

}
