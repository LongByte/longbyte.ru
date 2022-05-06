<?php

namespace Api\Sensors\Sensor;

/**
 * Class \Api\Sensors\Sensor\Collection
 */
class Collection extends \Api\Core\Base\Collection
{

    public function getByParams(string $strSensorApp, string $strSensorClass, string $strSensorName): ?Entity
    {
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

    public function getLastSort(): int
    {
        if ($this->count() > 0) {
            $obCollection = $this->getCollection();
            return end($obCollection)->getSort();
        }
        return 0;
    }

}
