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
                $this->normalize($obSensor->getSensorApp()) == $this->normalize($strSensorApp) &&
                $this->normalize($obSensor->getSensorDevice()) == $this->normalize($strSensorClass) &&
                $this->normalize($obSensor->getSensorName()) == $this->normalize($strSensorName)
            ) {
                return $obSensor;
            }
        }

        return null;
    }

    /**
     * 
     * @param string $string
     * @return string
     */
    private function normalize($string) {
        return str_replace(' ', '', $string);
    }

}