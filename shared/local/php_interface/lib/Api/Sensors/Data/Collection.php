<?php

namespace Api\Sensors\Data;

/**
 * Class \Api\Sensors\Data\Collection
 */
class Collection extends \Api\Core\Collection\Base {

    /**
     * 
     * @param int $iSensorId
     * @return null|\Api\Sensors\Data\Entity
     */
    public function getBySensorId(int $iSensorId) {
        /** @var \Api\Sensors\Data\Entity $obValue */
        foreach ($this->getCollection() as $obValue) {
            if ($obValue->getSensorId() == $iSensorId) {
                return $obValue;
            }
        }
        return null;
    }

}
