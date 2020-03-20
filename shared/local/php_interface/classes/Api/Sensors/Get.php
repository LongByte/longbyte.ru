<?

namespace Api\Sensors;

use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;

/**
 * class \Api\Sensors\Post
 */
class Get {

    private $obRequest = null;
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
        $this->obRequest = Context::getCurrent()->getRequest();
        $this->name = $this->obRequest->get('name');
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

        $date = $this->obRequest->get('date');
        if (strlen($date) > 0) {
            $obDate = DateTime::tryParse($date, 'd.m.Y');
        } else {
            $obDate = new DateTime();
        }
        $obDate->setTime(0, 0, 0);
        $obDateTo = clone $obDate;
        $obDateTo->add('+1day');

        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                'SYSTEM_ID' => $this->obSystem->getId(),
                'ACTIVE' => true,
        ));

        $arValuesFilter = array(
            'SENSOR.ACTIVE' => true,
            'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
        );
        if ($this->obSystem->isModeAvg()) {
            $arValuesFilter['DATE'] = $obDate;
        }
        if ($this->obSystem->isModeEach()) {
            $arValuesFilter['>=DATE'] = $obDate;
            $arValuesFilter['<DATE'] = $obDateTo;
        }

        $obValues = \Api\Sensors\Data\Model::getAll($arValuesFilter);


        foreach ($obValues as $obValue) {
            $obSensor = $obSensors->getByKey($obValue->getSensorId());
            $obValue->setSystemMode($this->obSystem->getMode());

            $valueMin = 0;
            $valueMax = 0;
            if ($this->obSystem->isModeAvg()) {
                $valueMin = $obValue->getSensorValueMin();
                $valueMax = $obValue->getSensorValueMax();
            }
            if ($this->obSystem->isModeEach()) {
                $valueMin = $obValue->getSensorValue();
                $valueMax = $obValue->getSensorValue();
            }

            if (!$obSensor->isAlert() && $obSensor->getAlertValueMax() != 0 && $valueMax > $obSensor->getAlertValueMax()) {
                $obSensor->setAlert();
                $obSensor->setAlertDirection(1);
            }

            if (!$obSensor->isAlert() && $obSensor->getAlertValueMin() != 0 && $valueMin < $obSensor->getAlertValueMin()) {
                $obSensor->setAlert();
                $obSensor->setAlertDirection(1);
            }

            $obSensor->addValue($obValue);
        }

        $arVue = array(
            'SYSTEM' => $this->obSystem,
            'SENSORS' => $obSensors,
            'DATE' => $obDate->format('d.m.Y'),
        );

        \LongByte\Vue::arrayKeyToLower($arVue);

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
