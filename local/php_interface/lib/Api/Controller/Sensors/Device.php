<?

namespace Api\Controller\Sensors;

/**
 * Class \Api\Controller\Sensors\Device
 */
class Device extends Edit
{

    public function delete()
    {
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $strDeviceName = $this->getRequest()->get('id');
        $strMode = $this->getRequest()->get('mode');

        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        foreach ($this->getSystem()->getSensorsCollection() as $obSensor) {

            if ($obSensor->getActive() || \Api\Sensors\Sensor\Model::normalize($obSensor->getSensorDevice()) != \Api\Sensors\Sensor\Model::normalize($strDeviceName)) {
                continue;
            }

            $arValuesFilter = array(
                'SENSOR_ID' => $obSensor->getId(),
                'SENSOR.SYSTEM_ID' => $this->getSystem()->getId(),
            );

            if ($strMode == 'data' || $strMode == 'sensor') {
                /** @var \Api\Sensors\Data\Collection $obValues */
                $obValues = \Api\Sensors\Data\Model::getAll($arValuesFilter);
                /** @var \Api\Sensors\Data\Entity $obValue */
                foreach ($obValues as $obValue) {
                    $obValue->delete();
                }
            }

            if ($strMode == 'sensor') {
                $obSensor->delete();
            }
        }

        $this->loadSystem();

        $iSort = 0;
        foreach ($this->getSystem()->getSensorsCollection() as $obSensor) {
            $iSort += 10;

            $obSensor->setSort($iSort);
            if ($obSensor->isChanged()) {
                $obSensor->save();
            }
        }

        return $this->get();
    }

}
