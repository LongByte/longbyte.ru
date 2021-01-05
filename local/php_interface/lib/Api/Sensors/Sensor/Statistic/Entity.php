<?php

namespace Api\Sensors\Sensor\Statistic;

/**
 * Class \Api\Sensors\Sensor\Statistic\Entity
 * 
 * @method mixed getValueMin()
 * @method $this setValueMin(mixed $mixedValueMin)
 * @method mixed getValueMax()
 * @method $this setValueMax(mixed $mixedValueMax)
 * @method mixed getValuesCount()
 * @method $this setValuesCount(mixed $mixedValuesCount)
 */
class Entity extends \Api\Core\Base\Virtual\Entity {

    /**
     *
     * @var array
     */
    protected static $arFields = array('VALUE_MIN', 'VALUE_MAX', 'VALUES_COUNT');

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

}
