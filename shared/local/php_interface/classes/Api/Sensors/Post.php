<?

namespace Api\Sensors;

use Bitrix\Main\Context;

/**
 * class \Api\Sensors\Post
 */
class Post {

    private $obRequest = null;
    private $rawPost = null;
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

    /**
     *
     * @var \Api\Sensors\Data\Collection 
     */
    private $obTodayValues = null;

    public function __construct() {
        $this->obRequest = Context::getCurrent()->getRequest();
        $this->rawPost = file_get_contents('php://input');
        $this->token = $this->obRequest->get('token');
    }

    public function post() {
        $arData = json_decode($this->rawPost);
        if (!$this->getSystem()) {
            return $this->exitAction();
        }

        if (!is_array($arData)) {
            $this->arResponse['success'] = false;
            $this->arResponse['errors'][] = 'Некорректные данные';
            return $this->exitAction();
        }

        $this->insertSensorsData($arData);
        return $this->exitAction();
    }

    public function get() {
        $obHttp = new \Bitrix\Main\Web\HttpClient();
        $rawGet = $obHttp->get('http://localhost:55555/');
        $arData = json_decode($rawGet);
        if (!$this->getSystem()) {
            return $this->exitAction();
        }

        if (!is_array($arData)) {
            $this->arResponse['success'] = false;
            $this->arResponse['errors'][] = 'Некорректные данные';
            return $this->exitAction();
        }

        $this->insertSensorsData($arData);
        return $this->exitAction();
    }

    /**
     * 
     * @return string
     */
    private function exitAction() {
        $this->sendAlerts();
        header('Content-Type: application/json');
        return json_encode($this->arResponse);
    }

    /**
     * 
     * @return boolean
     */
    private function getSystem() {
        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        $this->obSystem = \Api\Sensors\System\Model::getOne(array(
                '=TOKEN' => $this->token,
                'ACTIVE' => true
        ));

        if ($this->obSystem) {

            $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                    'SYSTEM_ID' => $this->obSystem->getId(),
            ));

            $this->obSystem->setSensorsCollection($obSensors);

            if ($this->obSystem->isModeAvg()) {

                $this->obTodayValues = \Api\Sensors\Data\Model::getAll(array(
                        'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
                        'DATE' => new \Bitrix\Main\Type\Date(),
                ));
            }

            return true;
        } else {
            $this->arResponse['success'] = false;
            $this->arResponse['errors'][] = 'Неверный токен';
        }

        return false;
    }

    /**
     * 
     * @param array $arData
     */
    private function insertSensorsData(array $arData) {

        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        foreach ($arData as $obInputValue) {
            $value = floatval(str_replace(',', '.', $obInputValue->SensorValue));
            $obSensor = $this->obSystem->getSensorsCollection()->getByParams($obInputValue->SensorApp, $obInputValue->SensorClass, $obInputValue->SensorName);

            if (is_null($obSensor)) {

                $obSensor = new \Api\Sensors\Sensor\Entity();
                $obSensor
                    ->setActive(true)
                    ->setSystem($this->obSystem)
                    ->setSensorApp($obInputValue->SensorApp)
                    ->setSensorClass($obInputValue->SensorClass)
                    ->setSensorrName($obInputValue->SensorName)
                    ->setSensorUnit($obInputValue->SensorUnit)
                    ->save()
                ;

                if (!$obSensor->isExist()) {
                    $this->arResponse['errors'][] = 'Невозможно создать сенсор. Данные: ' . print_r($obSensor->toArray(), true);
                    continue;
                }
            } else {
                if (!$obSensor->getActive())
                    continue;
            }

            if ($this->obSystem->isModeAvg()) {
                $obValue = $this->obTodayValues->getBySensorId($obSensor->getId());

                if (is_null($obValue)) {
                    $obValue = new \Api\Sensors\Data\Entity();
                    $obValue
                        ->setSensor($obSensor)
                        ->setDate(new \Bitrix\Main\Type\Date())
                        ->setSensorValueMin($value)
                        ->setSensorValue($value)
                        ->setSensorValueMax($value)
                        ->setSensorValues(1)
                        ->save()
                    ;

                    if (!$obValue->isExist()) {
                        $this->arResponse['errors'][] = 'Невозможно добавить данные. Данные: ' . print_r($obValue->toArray(), true);
                    }
                } else {
                    if ($value < $obValue->getSensorValueMin()) {
                        $obValue->setSensorValueMin($value);
                    }
                    if ($value > $obValue->getSensorValueMax()) {
                        $obValue->setSensorValueMax($value);
                    }
                    $fAvgValue = ($obValue->getSensorValue() * $obValue->getSensorValues() + $value) / ($obValue->getSensorValues() + 1);
                    $obValue->setSensorValue($fAvgValue);
                    $obValue->setSensorValues($obValue->getSensorValues() + 1);
                    $obValue->setSensor($obSensor);
                    $obValue->save();
                }
            }

            if ($this->obSystem->isModeEach()) {
                $obValue = new \Api\Sensors\Data\Entity();
                $obValue
                    ->setSensor($obSensor)
                    ->setDate(new \Bitrix\Main\Type\Date())
                    ->setSensorValue($value)
                    ->save()
                ;

                if (!$obValue->isExist()) {
                    $this->arResponse['errors'][] = 'Невозможно добавить данные. Данные: ' . print_r($obValue->toArray(), true);
                }
            }

            $this->checkAlert($obValue);
        }
    }

    /**
     * 
     * @param \Api\Sensors\Data\Entity $obValue
     */
    private function checkAlert(\Api\Sensors\Data\Entity $obValue) {

        $obSensor = $obValue->getSensor();

        $message = 'Значение на датчике ' . $obSensor->getSensorApp() . ' > ' . $obSensor->getSensorDevice() . ' > ' . $obSensor->getSensorName() . ' = ' . $obValue->getSensorValue() . $obSensor->getSensorUnit() . ' и ';

        if ($obSensor->getAlertValueMin() != 0 && $obValue->getSensorValue() < $obSensor->getAlertValueMin()) {
            $message .= 'меньше допустимого ' . $obSensor->getAlertValueMin();
            $obSensor->setAlert();
        }

        if ($obSensor->getAlertValueMax() != 0 && $obValue->getSensorValue() > $obSensor->getAlertValueMax()) {
            $message .= 'больше допустимого ' . $obSensor->getAlertValueMax();
            $obSensor->setAlert();
        }

        $message .= $obSensor->getSensorUnit();

        if ($obSensor->isAlert()) {
            $this->arResponse['alerts'][] = $message;
        }
    }

    /**
     * 
     */
    private function sendAlerts() {
        if (count($this->arResponse['alerts']) > 0 && strlen($this->arSystem['EMAIL']) > 0) {

            $strUrl = 'https://longbyte.ru/sensors/' . $this->obSystem->getName() . '-' . $this->obSystem->getToken() . '/';

            $message = 'Контроль сенсоров на системе <a href="' . $strUrl . '">' . $this->obSystem->getName() . '</a>. Некоторые значения вне допустимого диапазона.<br><br>';
            $message .= implode('<br>', $this->arResponse['alerts']);

            \Bitrix\Main\Mail\Event::send(array(
                'EVENT_NAME' => 'SENSORS_ALERT',
                'LID' => 's1',
                'C_FIELDS' => array(
                    'EMAIL_TO' => $this->obSystem->getEmail(),
                    'SUBJECT' => 'Оповещение системы контроля сенсоров на системе ' . $this->obSystem->getName(),
                    'MESSAGE' => $message
                )
            ));
        }
    }

}
