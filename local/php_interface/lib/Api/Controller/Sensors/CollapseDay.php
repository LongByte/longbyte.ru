<?

namespace Api\Controller\Sensors;

use Bitrix\Main\Type\DateTime;

/**
 * class \Api\Controller\Sensors\CollapseDay
 */
class CollapseDay extends \Api\Core\Base\Controller {

    private $arResponse = array(
        'success' => true,
    );

    /**
     * 
     * @return json
     */
    public function get() {

        /** @var \Api\Sensors\System\Collection $obSystems */
        /** @var \Api\Sensors\System\Entity $obSystem */
        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        $obSystems = \Api\Sensors\System\Model::getAll(array(
                'ACTIVE' => true
        ));

        $obYesterday = new DateTime();
        $obYesterday->setTime(0, 0, 0);
        $obYesterday->add('-1day');

        $obToday = new DateTime();
        $obToday->setTime(0, 0, 0);

        foreach ($obSystems as $obSystem) {

            $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                    'SYSTEM_ID' => $obSystem->getId(),
                    'ACTIVE' => true,
                    'LOG_MODE' => \Api\Sensors\Sensor\Table::MODE_EACH_LAST_DAY
            ));
            foreach ($obSensors as $obSensor) {

                $obValues = \Api\Sensors\Data\Model::getAll(array(
                        'SENSOR_ID' => $obSensor->getId(),
                        '>=DATE' => $obYesterday,
                        '<DATE' => $obToday,
                ));

                $fMinValue = null;
                $fMaxValue = null;
                $iValuesCount = 0;
                $fSum = 0;

                foreach ($obValues as $obValue) {
                    if (is_null($fMinValue) || $obValue->getValueMin() < $fMinValue) {
                        $fMinValue = $obValue->getValueMin();
                    }
                    if (is_null($fMaxValue) || $obValue->getValueMax() > $fMaxValue) {
                        $fMaxValue = $obValue->getValueMax();
                    }

                    $iValuesCount += $obValue->getValuesCount();
                    $fSum += $obValue->getValueAvg() * $obValue->getValuesCount();
                    $obValue->delete();
                }

                $fAvgValue = round($fSum / $iValuesCount, $obSensor->getPrecision());

                $obCollepseValue = new \Api\Sensors\Data\Entity();

                $obCollepseValue
                    ->setDate($obYesterday)
                    ->setSensorId($obSensor->getId())
                    ->setValueAvg($fAvgValue)
                    ->setValueMax($fMaxValue)
                    ->setValueMin($fMinValue)
                    ->setValuesCount($iValuesCount)
                    ->save()
                ;
            }
        }

        return $this->arResponse;
    }

}
