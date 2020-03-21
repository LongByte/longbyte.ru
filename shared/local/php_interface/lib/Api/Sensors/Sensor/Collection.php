<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Collection
 */
class Collection extends \Api\Core\Base\Collection {

    /**
     * 
     * @param string $strSensorApp
     * @param string $strSensorClass
     * @param string $strSensorName
     * @return null|\Api\Sensors\Sensor\Entity
     */
    public function getByParams(string $strSensorApp, string $strSensorClass, string $strSensorName) {

        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        foreach ($this->getCollection() as $obSensor) {
            if (
                $obSensor->getSensorApp() == $strSensorApp &&
                $obSensor->getSensorDevice() == $strSensorClass &&
                $obSensor->getSensorName() == $strSensorName
            ) {
                return $obSensor;
            }
        }

        return null;
    }

}
