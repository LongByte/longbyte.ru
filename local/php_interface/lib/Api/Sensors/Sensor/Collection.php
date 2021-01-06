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
     * @return \Api\Sensors\Sensor\Entity|null
     */
    public function getByParams(string $strSensorApp, string $strSensorClass, string $strSensorName): ?\Api\Sensors\Sensor\Entity {

        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        foreach ($this->getCollection() as $obSensor) {
            if (
                \Api\Sensors\Sensor\Model::normalize($obSensor->getSensorApp()) == \Api\Sensors\Sensor\Model::normalize($strSensorApp) &&
                \Api\Sensors\Sensor\Model::normalize($obSensor->getSensorDevice()) == \Api\Sensors\Sensor\Model::normalize($strSensorClass) &&
                \Api\Sensors\Sensor\Model::normalize($obSensor->getSensorName()) == \Api\Sensors\Sensor\Model::normalize($strSensorName)
            ) {
                return $obSensor;
            }
        }

        return null;
    }

}
