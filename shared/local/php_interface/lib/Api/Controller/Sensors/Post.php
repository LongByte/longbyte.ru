<?

namespace Api\Controller\Sensors;

/**
 * class \Api\Controller\Sensors\Post
 */
class Post extends \Api\Core\Base\Controller {

    private $saveEvery = 60;
    private $alertEvery = 5 * 60;
    private $token = null;
    private $arAlerts = array();
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

    /**
     *
     * @var \Bitrix\Main\Type\DateTime 
     */
    private $obLastAlert = null;

    /**
     *
     * @var \Bitrix\Main\Type\DateTime 
     */
    private $obLastSave = null;

    /**
     * 
     * @param string|null $strToken
     */
    public function __construct($strToken = null) {
        parent::__construct();
        $this->token = $this->obRequest->get('token');
        if (!is_null($strToken)) {
            $this->token = $strToken;
        }
    }

    public function post() {
        $this->resetResponse();
        $arData = json_decode($this->getPostData());
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

    public function emergencySave() {
        if (!is_null($this->obLastSave)) {
            $this->obTodayValues->save($this->arResponse['errors']);
            $this->obLastSave = new \Bitrix\Main\Type\DateTime();
            $this->arResponse['data']['last_save'] = $this->obLastSave->format('H:i:s d.m.Y');
        }
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
        if (is_null($this->obSystem)) {
            $this->loadSystem();
        }

        if ($this->obSystem) {
            if ($this->obSystem->getSensorsCollection()->count() <= 0) {
                $this->loadSensors();
            }

            if ($this->obSystem->isModeAvg()) {

                $obToday = new \Bitrix\Main\Type\Date();
                if (is_null($this->obTodayValues) || $obToday->getTimestamp() != $this->obTodayValues->getDate()->getTimestamp()) {
                    if (!is_null($this->obTodayValues) && $obToday->getTimestamp() != $this->obTodayValues->getDate()->getTimestamp()) {
                        $this->obTodayValues->save($this->arResponse['errors']);
                        $this->obLastSave = null;
                    }
                    $this->obTodayValues = \Api\Sensors\Data\Model::getAll(array(
                            'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
                            'DATE' => $obToday,
                    ));
                    $this->obTodayValues->setDate($obToday);
                }
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
     */
    private function loadSystem() {
        $this->obSystem = \Api\Sensors\System\Model::getOne(array(
                '=TOKEN' => $this->token,
                'ACTIVE' => true
        ));
    }

    /**
     * 
     */
    private function loadSensors() {
        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                'SYSTEM_ID' => $this->obSystem->getId(),
        ));

        if (!is_null($this->obSystem)) {
            $this->obSystem->setSensorsCollection($obSensors);
        }
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
            $this->arResponse['data']['read_values'] ++;
            $value = floatval(str_replace(',', '.', $obInputValue->SensorValue));
            $obSensor = $this->obSystem->getSensorsCollection()->getByParams($obInputValue->SensorApp, $obInputValue->SensorClass, $obInputValue->SensorName);

            if (is_null($obSensor)) {

                $obSensor = new \Api\Sensors\Sensor\Entity();
                $obSensor
                    ->setActive(true)
                    ->setSystem($this->obSystem)
                    ->setSensorApp($obInputValue->SensorApp)
                    ->setSensorDevice($obInputValue->SensorClass)
                    ->setSensorName($obInputValue->SensorName)
                    ->setSensorUnit($obInputValue->SensorUnit)
                    ->save()
                ;

                if (!$obSensor->isExists()) {
                    $this->arResponse['errors'][] = 'Невозможно создать сенсор. Данные: ' . print_r($obSensor->toArray(), true);
                    continue;
                }

                $this->obSystem->getSensorsCollection()->addItem($obSensor);
            } else {
                if (!$obSensor->getActive())
                    continue;
            }

            if ($this->obSystem->isModeAvg()) {
                $obValue = $this->obTodayValues->getBySensorId((int) $obSensor->getId());

                if (is_null($obValue)) {
                    $obValue = new \Api\Sensors\Data\Entity();
                    $obValue
                        ->setSensor($obSensor)
                        ->setDate(new \Bitrix\Main\Type\Date())
                        ->setSensorValueMin($value)
                        ->setSensorValue($value)
                        ->setSensorValueMax($value)
                        ->setSensorValues(1)
                        ->setLastValue((float) $value)
                    ;

                    $this->obTodayValues->addItem($obValue);
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
                    $obValue->setLastValue((float) $value);
                }
            }

            if ($this->obSystem->isModeEach()) {
                $obValue = new \Api\Sensors\Data\Entity();
                $obValue
                    ->setSensor($obSensor)
                    ->setDate(new \Bitrix\Main\Type\Date())
                    ->setSensorValue($value)
                    ->setLastValue((float) $value)
                    ->save()
                ;

                if (!$obValue->isExists()) {
                    $this->arResponse['errors'][] = 'Невозможно добавить данные. Данные: ' . print_r($obValue->toArray(), true);
                }
            }

            $this->checkAlert($obValue);
        }

        if ($this->obSystem->isModeAvg()) {
            if (is_null($this->obLastSave) || $this->obLastSave->getTimestamp() + $this->saveEvery < (new \Bitrix\Main\Type\DateTime())->getTimestamp()) {
                $this->obTodayValues->save($this->arResponse['errors']);
                $this->obLastSave = new \Bitrix\Main\Type\DateTime();
                $this->arResponse['data']['last_save'] = $this->obLastSave->format('H:i:s d.m.Y');
                $this->obSystem
                    ->setLastUpdate($this->obLastSave)
                    ->save()
                ;
                $this->loadSystem();
                $this->loadSensors();
            }
        }

        if ($this->obSystem->isModeEach()) {
            $this->obLastSave = new \Bitrix\Main\Type\DateTime();
            $this->arResponse['data']['last_save'] = $this->obLastSave->format('H:i:s d.m.Y');
            $this->obSystem
                ->setLastUpdate($this->obLastSave)
                ->save()
            ;
            $this->loadSystem();
            $this->loadSensors();
        }
    }

    /**
     * 
     * @param \Api\Sensors\Data\Entity $obValue
     */
    private function checkAlert(\Api\Sensors\Data\Entity $obValue) {

        $obSensor = $obValue->getSensor();

        $message = 'Значение на датчике ' . $obSensor->getSensorApp() . ' > ' . $obSensor->getSensorDevice() . ' > ' . $obSensor->getSensorName() . ' = ' . $obValue->getLastValue() . $obSensor->getSensorUnit() . ' и ';

        if ($obSensor->getAlertValueMin() != 0 && $obValue->getLastValue() < $obSensor->getAlertValueMin()) {
            $message .= 'меньше допустимого ' . $obSensor->getAlertValueMin();
            $obSensor->setAlert();
        }

        if ($obSensor->getAlertValueMax() != 0 && $obValue->getLastValue() > $obSensor->getAlertValueMax()) {
            $message .= 'больше допустимого ' . $obSensor->getAlertValueMax();
            $obSensor->setAlert();
        }

        $message .= $obSensor->getSensorUnit();

        if ($obSensor->isAlert()) {
            $this->arResponse['alerts'][] = $message;
            $this->arAlerts[] = $message;
        }
    }

    /**
     * 
     */
    private function sendAlerts() {
        if (
            count($this->arAlerts) > 0 &&
            strlen($this->obSystem->getEmail()) > 0 &&
            (
            is_null($this->obLastAlert) ||
            !is_null($this->obLastAlert) && $this->obLastAlert->getTimestamp() + $this->alertEvery < (new \Bitrix\Main\Type\DateTime())->getTimestamp()
            )
        ) {

            $this->arAlerts = array_unique($this->arAlerts);

            $strUrl = 'https://longbyte.ru/sensors/' . $this->obSystem->getName() . '-' . $this->obSystem->getToken() . '/';

            $message = 'Контроль сенсоров на системе <a href="' . $strUrl . '">' . $this->obSystem->getName() . '</a>. Некоторые значения вне допустимого диапазона.<br><br>';
            $message .= implode('<br>', $this->arAlerts);

            \Bitrix\Main\Mail\Event::send(array(
                'EVENT_NAME' => 'SENSORS_ALERT',
                'LID' => 's1',
                'C_FIELDS' => array(
                    'EMAIL_TO' => $this->obSystem->getEmail(),
                    'SUBJECT' => 'Оповещение системы контроля сенсоров на системе ' . $this->obSystem->getName(),
                    'MESSAGE' => $message
                )
            ));

            $this->arAlerts = array();
            $this->obLastAlert = new \Bitrix\Main\Type\DateTime();
        }
    }

    /**
     * 
     */
    private function resetResponse() {
        $this->arResponse['alerts'] = array();
        $this->arResponse['errors'] = array();
        $this->arResponse['success'] = true;
    }

}
