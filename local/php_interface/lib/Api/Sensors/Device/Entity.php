<?php

namespace Api\Sensors\Device;

/**
 * Class \Api\Sensors\Device\Entity
 * 
 * @method mixed getName()
 * @method $this setName(mixed $mixedName)
 */
class Entity extends \Api\Core\Base\Virtual\Entity {

    /**
     *
     * @var string
     */
    protected static $_primaryField = 'NAME';
    
    /**
     *
     * @var array
     */
    protected static $arFields = array('NAME');

    /**
     *
     * @var \Api\Sensors\Sensor\Collection
     */
    protected $_obSensorsCollection = null;

    /**
     * 
     * @param array $data
     */
    public function __construct(array $data = array()) {
        if (!$data) {
            $data = array_fill_keys($this->getFields(), '');
        }
        parent::__construct($data);
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
     * @param type $arData
     * @return array
     */
    public function toArray($arData = null): array {
        $arData = parent::toArray($arData);
        $arData['sensors'] = $this->getSensorsCollection()->toArray();
        return $arData;
    }

}
