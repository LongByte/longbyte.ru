<?

namespace Api\Controller\Sensors;

/**
 * Class \Api\Controller\Sensors\Post
 */
class Post extends \Api\Core\Base\Controller
{

    private int $saveEvery = 60;
    private int $alertEvery = 5 * 60;
    private ?string $token = null;

    private array $arResponse = array(
        'data' => array(),
        'errors' => array(),
        'success' => true,
    );

    private ?\Api\Sensors\System\Entity $obSystem = null;
    private ?\Api\Sensors\Data\Collection $obTodayValues = null;
    private ?\Bitrix\Main\Type\DateTime $obLastAlert = null;
    private ?\Api\Core\Base\Collection $obAlerts = null;
    private ?\Bitrix\Main\Type\DateTime $obLastSave = null;

    public function __construct(string $strToken = null)
    {
        parent::__construct();
        $this->token = $this->getRequest()->get('token');
        if (!is_null($strToken)) {
            $this->token = $strToken;
        }
        $this->obAlerts = new \Api\Core\Base\Collection();
        $this->obAlerts->setUniqueMode(true);
    }

    public function post()
    {
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

    public function get()
    {
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

    public function emergencySave(): void
    {
        if (!is_null($this->obLastSave)) {
            $this->obTodayValues->save($this->arResponse['errors']);
            $this->obLastSave = new \Bitrix\Main\Type\DateTime();
            $this->arResponse['data']['last_save'] = $this->obLastSave->format('H:i:s d.m.Y');
        }
    }

    public function getDebug(): string
    {
        return json_encode(array(
            'post_data' => $this->getPostData(),
            'sensors' => !is_null($this->obSystem) ? $this->obSystem->getSensorsCollection()->toArray() : array(),
            'today_values' => !is_null($this->obTodayValues) ? $this->obTodayValues->toArray() : array(),
            'response' => $this->arResponse,
        ));
    }

    protected function exitAction(): string
    {
        $this->sendAlerts();
        header('Content-Type: application/json');
        return json_encode($this->arResponse);
    }

    private function getSystem(): bool
    {
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

                $arTodayValue = \Api\Sensors\Data\Model::getAllAsArray(array(
                    'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
                    '>=DATE' => $obDate,
                    '<DATE' => $obDateTo,
                ), 0, 0, array(
                    'order' => array('DATE' => 'DESC'),
                ));

                $this->obTodayValues = new \Api\Sensors\Data\Collection();
                foreach ($arTodayValue as $arValue) {
                    if (!$this->obTodayValues->getBySensorId($arValue['SENSOR_ID'])) {
                        $obValue = new \Api\Sensors\Data\Entity($arValue['ID'], $arValue);
                        $this->obTodayValues->addItem($obValue);
                    }
                }
                unset($arTodayValue);

                $this->obTodayValues->setDate($obToday);
            }

            return true;
        } else {
            $this->arResponse['success'] = false;
            $this->arResponse['errors'][] = 'Неверный токен';
        }

        return false;
    }

    private function loadSystem(): void
    {
        $this->obSystem = \Api\Sensors\System\Model::getOne(array(
            '=TOKEN' => $this->token,
            'ACTIVE' => true,
        ));
    }

    private function loadSensors(): void
    {
        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
            'SYSTEM_ID' => $this->obSystem->getId(),
        ));

        if (!is_null($this->obSystem)) {
            $this->obSystem->setSensorsCollection($obSensors);

            /** @var \Api\Sensors\Sensor\Entity $obSensor */
            foreach ($this->obSystem->getSensorsCollection() as $obSensor) {
                $obAlert = $this->getAlertCollection()->getByKey($obSensor->getId());
                if (is_null($obAlert)) {
                    $obAlert = $obSensor->getAlert();
                    $this->getAlertCollection()->addItem($obAlert);
                } else {
                    if (!$obSensor->hasAlert()) {
                        $obSensor->setAlert($obAlert);
                    }
                }
            }
        }
    }

    private function insertSensorsData(array $arData): void
    {

        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        foreach ($arData as $obInputValue) {
            $this->arResponse['data']['read_values']++;
            $value = floatval(str_replace(',', '.', $obInputValue->SensorValue));

            $obSensor = $this->obSystem->getSensorsCollection()->getByParams($obInputValue->SensorApp, $obInputValue->SensorClass, $obInputValue->SensorName);

            if (is_null($obSensor)) {

                $obSensor = new \Api\Sensors\Sensor\Entity();
                $obSensor
                    ->setActive(false)
                    ->setSystem($this->obSystem)
                    ->setSensorApp($obInputValue->SensorApp)
                    ->setSensorDevice($obInputValue->SensorClass)
                    ->setSensorName($obInputValue->SensorName)
                    ->setSensorUnit($obInputValue->SensorUnit)
                    ->setLogMode(\Api\Sensors\Sensor\Table::MODE_EACH_LAST_DAY)
                    ->setAlertEnable(false)
                    ->setSort($this->obSystem->getSensorsCollection()->getLastSort() + 10)
                    ->save()
                ;

                if (!$obSensor->isExists()) {
                    $this->arResponse['errors'][] = 'Невозможно создать сенсор. Ошибка: ' . print_r($obSensor->getDBResult()->getErrorMessages(), true) . '. Данные: ' . print_r($obSensor->toArray(), true);
                    continue;
                }

                $obSensor->setNew();
                $this->obSystem->getSensorsCollection()->addItem($obSensor);
                continue;
            } else {
                if (!$obSensor->getActive())
                    continue;
            }

            try {
                if (strlen($obSensor->getModifier()) > 0) {
                    $strModifier = $obSensor->getModifier();
                    $strModifier = preg_replace('/[^\d\+\*\/\-\.]/', '', $strModifier);
                    if (preg_match('/^[\+\*\/\-]\d+(\.\d+)?([\+\*\/\-]\d+(\.\d+)?)?$/', $strModifier)) {
                        $strModifier = preg_replace('/^([\+\*\/\-]\d+(\.\d+)?)(([\+\*\/\-]\d+(\.\d+)?)?)$/', '$1)$3', $strModifier);
                        $fModifiedValue = @eval('return ($value' . $strModifier . ';');
                        if (is_numeric($fModifiedValue)) {
                            $value = $fModifiedValue;
                        }
                    }
                }
            } catch (\ParseError $exc) {

            }

            $value = round($value, (int) $obSensor->getPrecision());

            if ($obSensor->getIgnoreLess() != 0 && $value < $obSensor->getIgnoreLess()) {
                continue;
            }

            if ($obSensor->getIgnoreMore() != 0 && $value > $obSensor->getIgnoreMore()) {
                continue;
            }

            $obValue = $this->obTodayValues->getBySensorId((int) $obSensor->getId());

            $obDate = new \Bitrix\Main\Type\DateTime();
            $obDate->setTime(0, 0, 0);
            if ($obSensor->isModeAvg()) {

            }

            if ($obSensor->isModeEach() || $obSensor->isModeEachLastDay()) {
                $iRate = 5 * 60;
                $obTime = new \Bitrix\Main\Type\DateTime();
                $iDiff = floor(($obTime->getTimestamp() - $obDate->getTimestamp()) / $iRate) * $iRate;
                $obDate->add("+{$iDiff}seconds");
            }

            if (
                is_null($obValue) //
                ||
                (!is_null($obValue) && $obValue->getDate()->getTimestamp() != $obDate->getTimestamp())
            ) {
                if (!is_null($obValue)) {
                    $obValue->save();
                    $this->obTodayValues->removeByKey($obValue->getId());
                    unset($obValue);
                }

                $obValue = new \Api\Sensors\Data\Entity();
                $obValue
                    ->setSensor($obSensor)
                    ->setDate($obDate)
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
                $obValue->setValuesCount($obValue->getValuesCount() + 1);
                $obValue->setSensor($obSensor);
                $obValue->setValue($value);
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

    private function checkAlert(\Api\Sensors\Data\Entity $obValue): void
    {

        $obSensor = $obValue->getSensor();
        /** @var \Api\Sensors\Alert\Entity $obAlert */
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
            if ($obSensor->getAlert()->getValueMin() == 0 || $obValue->getValue() < $obSensor->getAlert()->getValueMin()) {
                $obSensor->getAlert()->setAlert(true);
                $obSensor->getAlert()->setTooLow();
                $obSensor->getAlert()->setValueMin($obValue->getValue());
            }
        }

        if ($obSensor->getAlertValueMax() != 0 && $obValue->getValue() > $obSensor->getAlertValueMax()) {
            if ($obSensor->getAlert()->getValueMax() == 0 || $obValue->getValue() > $obSensor->getAlert()->getValueMax()) {
                $obSensor->getAlert()->setAlert(true);
                $obSensor->getAlert()->setTooHigh();
                $obSensor->getAlert()->setValueMax($obValue->getValue());
            }
        }
    }

    private function sendAlerts(): void
    {
        if (
            is_null($this->obLastAlert)
            ||
            !is_null($this->obLastAlert) && $this->obLastAlert->getTimestamp() + $this->alertEvery < (new \Bitrix\Main\Type\DateTime())->getTimestamp()
        ) {

            $arEmailAlerts = array();
            $arTelegramAlerts = array();

            /** @var \Api\Sensors\Sensor\Entity $obSensor */
            foreach ($this->obSystem->getSensorsCollection() as $obSensor) {
                if ($obSensor->getActive() && $obSensor->getAlert()->isAlert() && $obSensor->isAllowAlert()) {
                    $arEmailAlerts[] = $obSensor->getAlert()->getEmailMessage();
                    $arTelegramAlerts[] = $obSensor->getAlert()->getTelegramMessage();
                    $obSensor->getAlert()->setAlert(false);
                }
                if ($obSensor->isNew()) {
                    $arEmailAlerts[] = 'Обнаружен новый датчик: ' . $obSensor->getSensorApp() . ' > ' . $obSensor->getSensorDevice() . ' > ' . $obSensor->getSensorName() . ' . Перейдите в настройки, чтобы активировать его.';
                    $arTelegramAlerts[] = '➕ Обнаружен новый датчик: ' . $obSensor->getSensorApp() . ' > ' . $obSensor->getSensorDevice() . ' > ' . $obSensor->getSensorName() . ' . Перейдите в настройки, чтобы активировать его.';
                    $obSensor->setNew(false);
                }
            }

            if (count($arEmailAlerts) > 0) {
                $strUrl = 'https://longbyte.ru/sensors/' . $this->obSystem->getNameToken() . '/';

                $message = 'Контроль сенсоров на системе <a href="' . $strUrl . '">' . $this->obSystem->getName() . '</a>. Некоторые значения вне допустимого диапазона.<br><br>';
                $message .= implode('<br>', $arEmailAlerts);

                if (strlen($this->obSystem->getEmail()) > 0) {
                    \Bitrix\Main\Mail\Event::send(array(
                        'EVENT_NAME' => 'SENSORS_ALERT',
                        'LID' => 's1',
                        'C_FIELDS' => array(
                            'EMAIL_TO' => $this->obSystem->getEmail(),
                            'SUBJECT' => 'Оповещение системы контроля сенсоров на системе ' . $this->obSystem->getName(),
                            'MESSAGE' => $message,
                        ),
                    ));
                }

                /** @var \Api\Sensors\Telegram\Collection $obTelegrams */
                $obTelegrams = \Api\Sensors\Telegram\Model::getAll(array(
                    'SYSTEM_ID' => $this->obSystem->getId(),
                    'ACTIVE' => 1,
                ));

                if ($obTelegrams->count() > 0) {
                    $message = 'Контроль сенсоров на системе <a href="' . $strUrl . '">' . $this->obSystem->getName() . '</a>' . "\n";
                    $message .= implode("\n", $arTelegramAlerts);

                    $obBot = new \TelegramBot\Api\Client(\Api\Sensors\Telegram\Model::getToken());
                    /** @var \Api\Sensors\Telegram\Entity $obTelegram */
                    foreach ($obTelegrams as $obTelegram) {
                        $obBot->sendMessage($obTelegram->getChatId(), $message, 'html', false);
                    }
                }

                $this->obLastAlert = new \Bitrix\Main\Type\DateTime();
            }
        }
    }

    private function resetResponse(): void
    {
        $this->arResponse['errors'] = array();
        $this->arResponse['success'] = true;
    }

    private function getAlertCollection(): \Api\Core\Base\Collection
    {
        return $this->obAlerts;
    }

}
