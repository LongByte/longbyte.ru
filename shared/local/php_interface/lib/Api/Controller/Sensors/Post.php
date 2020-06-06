<?

namespace Api\Controller\Sensors;

/**
 * class \Api\Controller\Sensors\Post
 */
class Post extends \Api\Core\Base\Controller {

    private $saveEvery = 60;
    private $alertEvery = 5 * 60;
    private $token = null;
    private $arResponse = array(
        'data' => array(),
        'errors' => array(),
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
     * @var \Api\Core\Base\Collection
     */
    private $obAlerts = null;

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
        $this->obAlerts = new \Api\Core\Base\Collection();
        $this->obAlerts->setUniqueMode(true);
    }

    public function post() {
        $this->resetResponse();
        $arData = json_decode($this->getPostData());
        if (!$this->getSystem()) {
            return $this->exitAction();
        }

        if (!is_array($arData)) {
            $this->obSystem
                ->setLastReceive(new \Bitrix\Main\Type\DateTime())
                ->save()
            ;
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
            $this->obSystem
                ->setLastReceive(new \Bitrix\Main\Type\DateTime())
                ->save()
            ;
            $this->arResponse['success'] = false;
            $this->arResponse['errors'][] = 'Некорректные данные';
            return $this->exitAction();
        }

        $this->insertSensorsData($arData);
        return $this->exitAction();
    }

    /**
     * 
     */
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

            $obDate = new \Bitrix\Main\Type\DateTime();
            $obDate->setTime(0, 0, 0);
            $obDateTo = clone $obDate;
            $obDateTo->add('+1day');

            $obToday = new \Bitrix\Main\Type\Date();
            if (is_null($this->obTodayValues) || $obToday->getTimestamp() != $this->obTodayValues->getDate()->getTimestamp()) {
                if (!is_null($this->obTodayValues) && $obToday->getTimestamp() != $this->obTodayValues->getDate()->getTimestamp()) {
                    $this->obTodayValues->save($this->arResponse['errors']);
                    $this->obLastSave = null;
                }
                $this->obTodayValues = \Api\Sensors\Data\Model::getAll(array(
                        'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
                        '>=DATE' => $obDate,
                        '<DATE' => $obDateTo,
                ));
                $this->obTodayValues->setDate($obToday);
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
                    ->setLogMode(Api\Sensors\Sensor\Table::MODE_AVG)
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

            if ($obSensor->getIgnoreLess() != 0 && $value < $obSensor->getIgnoreLess()) {
                continue;
            }

            if ($obSensor->getIgnoreMore() != 0 && $value > $obSensor->getIgnoreMore()) {
                continue;
            }

            if ($obSensor->isModeAvg()) {
                $obValue = $this->obTodayValues->getBySensorId((int) $obSensor->getId());

                if (is_null($obValue)) {
                    $obValue = new \Api\Sensors\Data\Entity();
                    $obValue
                        ->setSensor($obSensor)
                        ->setDate(new \Bitrix\Main\Type\Date())
                        ->setValueMin($value)
                        ->setValueAvg($value)
                        ->setValueMax($value)
                        ->setValue($value)
                        ->setValuesCount(1)
                    ;

                    $this->obTodayValues->addItem($obValue);
                } else {
                    if ($value < $obValue->getValueMin()) {
                        $obValue->setValueMin($value);
                    }
                    if ($value > $obValue->getValueMax()) {
                        $obValue->setValueMax($value);
                    }
                    $fAvgValue = ($obValue->getValueAvg() * $obValue->getValuesCount() + $value) / ($obValue->getValuesCount() + 1);
                    $obValue->setValueAvg($fAvgValue);
                    $obValue->getValuesCount($obValue->getValuesCount() + 1);
                    $obValue->setSensor($obSensor);
                    $obValue->setValue($value);
                }
            }

            if ($obSensor->isModeEach() || $obSensor->isModeEachLastDay()) {
                $obValue = new \Api\Sensors\Data\Entity();
                $obValue
                    ->setSensor($obSensor)
                    ->setDate(new \Bitrix\Main\Type\DateTime())
                    ->setValue($value)
                    ->save()
                ;

                if (!$obValue->isExists()) {
                    $this->arResponse['errors'][] = 'Невозможно добавить данные. Данные: ' . print_r($obValue->toArray(), true);
                }
            }

            $this->checkAlert($obValue);
        }

        if (is_null($this->obLastSave) || $this->obLastSave->getTimestamp() + $this->saveEvery < (new \Bitrix\Main\Type\DateTime())->getTimestamp()) {
            $this->obTodayValues->save($this->arResponse['errors']);
            $this->obLastSave = new \Bitrix\Main\Type\DateTime();
            $this->arResponse['data']['last_save'] = $this->obLastSave->format('H:i:s d.m.Y');

            $this->loadSystem();
            $this->loadSensors();
            $this->obSystem
                ->setLastUpdate($this->obLastSave)
                ->setLastReceive($this->obLastSave)
                ->save()
            ;
        }
    }

    /**
     * 
     * @param \Api\Sensors\Data\Entity $obValue
     */
    private function checkAlert(\Api\Sensors\Data\Entity $obValue) {

        $obSensor = $obValue->getSensor();

        $obAlert = $this->getAlertCollection()->getByKey($obSensor->getId());
        if (is_null($obAlert)) {
            $obAlert = $obSensor->getAlert();
            $this->getAlertCollection()->addItem($obAlert);
        } else {
            if (!$obSensor->hasAlert()) {
                $obSensor->setAlert($obAlert);
            }
        }

        if ($obSensor->getAlertValueMin() != 0 && $obValue->getValue() < $obSensor->getAlertValueMin()) {
            $obSensor->getAlert()->setAlert(true);
            $obSensor->getAlert()->setDirection(-1);
            if ($obSensor->getAlert()->getValueMin() == 0 || $obValue->getValue() < $obSensor->getAlert()->setValueMin()) {
                $obSensor->getAlert()->setValueMin($obValue->getValue());
            }
        }

        if ($obSensor->getAlertValueMax() != 0 && $obValue->getValue() > $obSensor->getAlertValueMax()) {
            $obSensor->getAlert()->setAlert(true);
            $obSensor->getAlert()->setDirection(1);
            if ($obSensor->getAlert()->getValueMax() == 0 || $obValue->getValue() > $obSensor->getAlert()->setValueMax()) {
                $obSensor->getAlert()->setValueMax($obValue->getValue());
            }
        }
    }

    /**
     * 
     */
    private function sendAlerts() {
        if (
            $this->getAlertCollection()->count() > 0 &&
            strlen($this->obSystem->getEmail()) > 0 &&
            (
            is_null($this->obLastAlert) ||
            !is_null($this->obLastAlert) && $this->obLastAlert->getTimestamp() + $this->alertEvery < (new \Bitrix\Main\Type\DateTime())->getTimestamp()
            )
        ) {

            $obNow = new \Bitrix\Main\Type\DateTime();

            $arAlerts = array();

            /** @var \Api\Sensors\Sensor\Entity $obSensor */
            foreach ($this->obSystem->getSensorsCollection() as $obSensor) {
                if ($obSensor->getActive() && $obSensor->getAlert()->isAlert() && $obSensor->getAlertMuteTill()->getTimestamp() < $obNow->getTimestamp()) {

                    $message = '';
                    $message .= 'Значение на датчике ' . $obSensor->getSensorApp() . ' > ' . $obSensor->getSensorDevice() . ' > ' . $obSensor->getSensorName() . ' = ' . $obSensor->getValue() . $obSensor->getSensorUnit() . ' и ';
                    if ($obSensor->getAlert()->getDirection() == -1) {
                        $message .= 'меньше допустимого ' . $obSensor->getAlert()->getValueMin();
                    }
                    if ($obSensor->getAlert()->getDirection() == 1) {
                        $message .= 'больше допустимого ' . $obSensor->getAlert()->getValueMax();
                    }
                    $message .= $obSensor->getSensorUnit();
                    $arAlerts[] = $message;

                    $obSensor->getAlert()->setAlert(false);
                }
            }

            if (count($arAlerts) > 0) {

                $strUrl = 'https://longbyte.ru/sensors/' . $this->obSystem->getName() . '-' . $this->obSystem->getToken() . '/';

                $message = 'Контроль сенсоров на системе <a href="' . $strUrl . '">' . $this->obSystem->getName() . '</a>. Некоторые значения вне допустимого диапазона.<br><br>';
                $message .= implode('<br>', $arAlerts);

                \Bitrix\Main\Mail\Event::send(array(
                    'EVENT_NAME' => 'SENSORS_ALERT',
                    'LID' => 's1',
                    'C_FIELDS' => array(
                        'EMAIL_TO' => $this->obSystem->getEmail(),
                        'SUBJECT' => 'Оповещение системы контроля сенсоров на системе ' . $this->obSystem->getName(),
                        'MESSAGE' => $message
                    )
                ));

                $this->obLastAlert = new \Bitrix\Main\Type\DateTime();
            }
        }
    }

    /**
     * 
     */
    private function resetResponse() {
        $this->arResponse['errors'] = array();
        $this->arResponse['success'] = true;
    }

    /**
     * 
     * @return \Api\Core\Base\Collection
     */
    private function getAlertCollection() {
        return $this->obAlerts;
    }

}
