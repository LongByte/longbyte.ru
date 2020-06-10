<?

namespace Api\Controller\Sensors;

use Bitrix\Main\Type\DateTime;

/**
 * class \Api\Controller\Sensors\Get
 */
class Get extends \Api\Core\Base\Controller {

    private $token = null;
    private $name = null;
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
        $this->name = $this->obRequest->get('name');
        $this->token = $this->obRequest->get('token');
    }

    /**
     * 
     * @return json
     */
    public function get() {

        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $obToday = new DateTime();
        $obToday->setTime(0, 0, 0);

        $date = $this->obRequest->get('date');
        if (strlen($date) > 0) {
            $obDate = new DateTime($date, 'd.m.Y');
        } else {
            $obDate = new DateTime();
        }
        $obDate->setTime(0, 0, 0);

        $bToday = $obToday->getTimestamp() == $obDate->getTimestamp();

        $obDateTo = clone $obDate;
        $obDateTo->add('+1day');

        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                'SYSTEM_ID' => $this->obSystem->getId(),
                'ACTIVE' => true,
        ));

        $arValuesFilter = array(
            'SENSOR.ACTIVE' => true,
            'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
            '>=DATE' => $obDate,
            '<DATE' => $obDateTo,
        );

        $obValues = \Api\Sensors\Data\Model::getAll($arValuesFilter);

        foreach ($obValues as $obValue) {
            $obSensor = $obSensors->getByKey($obValue->getSensorId());
            $obSensor->setToday($bToday);

            $this->obSystem->getSensorsCollection()->addItem($obSensor);
            $obSensor->setSystem($this->obSystem);

            $obSensor->getValuesCollection()->addItem($obValue);
            $obValue->setSensor($obSensor);

            $valueMin = $obValue->getValueMin();
            $valueMax = $obValue->getValueMax();

            if ($obSensor->getAlertValueMax() != 0 && $valueMax > $obSensor->getAlertValueMax()) {
                $obSensor->getAlert()->setAlert(true);
                $obSensor->getAlert()->setDirection(1);
            }

            if ($obSensor->getAlertValueMin() != 0 && $valueMin < $obSensor->getAlertValueMin()) {
                $obSensor->getAlert()->setAlert(true);
                $obSensor->getAlert()->setDirection(-1);
            }
        }

        $arVue = array(
            'system' => $this->obSystem->toArray(),
            'sensors' => $obSensors->toArray(),
            'date' => $obDate->format('d.m.Y'),
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
    private function loadSystem() {

        $this->obSystem = \Api\Sensors\System\Model::getOne(array(
                '=NAME' => $this->name,
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
