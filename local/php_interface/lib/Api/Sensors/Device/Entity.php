<?php

namespace Api\Sensors\Device;

/**
 * Class \Api\Sensors\Device\Entity
 *
 * @method mixed getName()
 * @method $this setName(mixed $mixedName)
 */
class Entity extends \Api\Core\Base\Virtual\Entity
{

    protected static string $_primaryField = 'NAME';
    protected static array $arFields = array('NAME');
    protected ?\Api\Sensors\Sensor\Collection $_obSensorsCollection = null;

    public function __construct(array $data = array())
    {
        if (!$data) {
            $data = array_fill_keys($this->getFields(), '');
        }
        parent::__construct($data);
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

    public function toArray($arData = null): array
    {
        $arData = parent::toArray($arData);
        $arData['sensors'] = $this->getSensorsCollection()->toArray();
        return $arData;
    }

}
