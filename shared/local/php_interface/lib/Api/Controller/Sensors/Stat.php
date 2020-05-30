<?

namespace Api\Controller\Sensors;

use Bitrix\Main\Type\DateTime;

/**
 * class \Api\Controller\Sensors\Stat
 */
class Stat extends \Api\Core\Base\Controller {

    private $token = null;
    private $arResponse = array(
        'data' => array(),
        'errors' => array(),
        'alerts' => array(),
        'success' => true,
    );

    /**
     *
     * @var \Api\Sensors\System\Entity
     */
    private $obSystem = null;

    public function __construct() {
        parent::__construct();
        $this->token = $this->obRequest->get('token');
    }

    public function get() {

        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        if (!$this->getSystem()) {
            return $this->exitAction();
        }

        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                'SYSTEM_ID' => $this->obSystem->getId(),
                'ACTIVE' => true,
        ));

        $arValuesFilter = array(
            'SENSOR.ACTIVE' => true,
            'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
        );

        $obValues = \Api\Sensors\Data\Model::getAll($arValuesFilter);

        foreach ($obValues as $obValue) {
            $obSensor = $obSensors->getByKey($obValue->getSensorId());

            $this->obSystem->getSensorsCollection()->addItem($obSensor);
            $obSensor->setSystem($this->obSystem);

            $obSensor->getValuesCollection()->addItem($obValue);
            $obValue->setSensor($obSensor);

            $valueMin = 0;
            $valueMax = 0;
            if ($this->obSystem->isModeAvg()) {
                $valueMin = $obValue->getSensorValueMin();
                $valueMax = $obValue->getSensorValueMax();
            }
            if ($this->obSystem->isModeEach()) {
//                $valueMin = $obValue->getSensorValue();
//                $valueMax = $obValue->getSensorValue();
            }

            if (!$obSensor->isAlert() && $obSensor->getAlertValueMax() != 0 && $valueMax > $obSensor->getAlertValueMax()) {
                $obSensor->setAlert();
                $obSensor->setAlertDirection(1);
            }

            if (!$obSensor->isAlert() && $obSensor->getAlertValueMin() != 0 && $valueMin < $obSensor->getAlertValueMin()) {
                $obSensor->setAlert();
                $obSensor->setAlertDirection(-1);
            }
        }

        $arVue = array(
            'system' => $this->obSystem->toArray(),
            'sensors' => $obSensors->toArray(),
        );

        $this->arResponse['data'] = $arVue;

        return $this->exitAction();
    }

    /**
     * 
     * @return string
     */
    private function exitAction() {
        $this->arrayValueToNumber($this->arResponse);
        header('Content-Type: application/json');
        return json_encode($this->arResponse);
    }

    /**
     * 
     * @param array $array
     */
    private function arrayValueToNumber(array &$array) {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                self::arrayValueToNumber($value);
            }
            if (is_numeric($value)) {
                $value = round($value, 3);
            }
        }
    }

    /**
     * 
     * @return boolean
     */
    private function getSystem() {

        $this->obSystem = \Api\Sensors\System\Model::getOne(array(
                '=TOKEN' => $this->token,
                'ACTIVE' => true
        ));

        if ($this->obSystem) {
            return true;
        } else {
            $this->arResponse['success'] = false;
            $this->arResponse['errors'][] = 'Неверный токен';
        }

        return false;
    }

}
