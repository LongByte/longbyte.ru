<?

namespace Api\Controller\Sensors;

/**
 * Class \Api\Controller\Sensors\Merge
 */
class Merge extends Edit
{

    public function post()
    {
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        /** @var \Api\Sensors\Sensor\Entity $obSensorFrom */
        $obSensorFrom = $this->getSystem()->getSensorsCollection()->getByKey($this->getRequest()->get('from_id'));
        /** @var \Api\Sensors\Sensor\Entity $obSensorTo */
        $obSensorTo = $this->getSystem()->getSensorsCollection()->getByKey($this->getRequest()->get('to_id'));

        if (!is_null($obSensorFrom) && !is_null($obSensorTo)) {

            /** @var \Api\Sensors\Data\Collection $obValuesFrom */
            $obValuesFrom = \Api\Sensors\Data\Model::getAll(array(
                'SENSOR_ID' => $obSensorFrom->getId(),
            ));

            /** @var \Api\Sensors\Data\Collection $obValuesTo */
            $obValuesTo = \Api\Sensors\Data\Model::getAll(array(
                'SENSOR_ID' => $obSensorTo->getId(),
            ));

            /** @var \Api\Sensors\Data\Entity $obValueFrom */
            foreach ($obValuesFrom as $obValueFrom) {
                $obValueTo = $obValuesTo->getByDateAndSensorId($obValueFrom->getDate(), $obSensorTo->getId());
                if (is_null($obValueTo)) {
                    $obValueTo = new \Api\Sensors\Data\Entity();
                    $obValueTo
                        ->setDate($obValueFrom->getDate())
                        ->setSensorId($obValueFrom->getSensorId())
                    ;
                }
                $fSumValues = (float) $obValueTo->getValueAvg() * (int) $obValueTo->getValuesCount() + (float) $obValueFrom->getValueAvg() * (int) $obValueFrom->getValuesCount();

                $obValueTo
                    ->setValueMin(min(array($obValueTo->getValueMin(), $obValueFrom->getValueMin())))
                    ->setValueMax(max(array($obValueTo->getValueMax(), $obValueFrom->getValueMax())))
                    ->setValueAvg($fSumValues / ((int) $obValueTo->getValuesCount() + (int) $obValueFrom->getValuesCount()))
                    ->setValuesCount((int) $obValueTo->getValuesCount() + (int) $obValueFrom->getValuesCount())
                    ->save()
                ;

                $obValueFrom->delete();
            }

            $obSensorFrom->delete();
        }

        return $this->get();
    }

}
