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
    private $arSystem = null;
    private $arSensors = null;
    private $arTodayValues = null;

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
        $arSystem = SensorsSystemTable::getRow(array(
                'filter' => array(
                    '=UF_TOKEN' => $this->token,
                    'UF_ACTIVE' => true
                ),
        ));

        if ($arSystem) {
            $this->arSystem = $arSystem;

            $rsSensors = SensorsSensorsTable::getList(array(
                    'filter' => array(
                        'UF_SYSTEM_ID' => $this->arSystem ['ID']
                    ),
            ));

            while ($arSensor = $rsSensors->fetch()) {
                $this->arSensors
                    [$arSensor['UF_SENSOR_APP']]
                    [$arSensor['UF_SENSOR_DEVICE']]
                    [$arSensor['UF_SENSOR_NAME']] = $arSensor;
            }

            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_AVG) {
                $rsValues = SensorsDataTable::getList(array(
                        'filter' => array(
                            'SENSOR.UF_SYSTEM_ID' => $this->arSystem['ID'],
                            'UF_DATE' => new \Bitrix\Main\Type\Date(),
                        ),
                ));

                while ($arValue = $rsValues->fetch()) {
                    $this->arTodayValues
                        [$arValue['UF_SENSOR_ID']] = $arValue;
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
     * @param array $arData
     */
    private function insertSensorsData(array $arData) {

        foreach ($arData as $obValue) {
            $value = floatval(str_replace(',', '.', $obValue->SensorValue));
            $arSensor = $this->getSensor($obValue->SensorApp, $obValue->SensorClass, $obValue->SensorName);

            if (is_null($arSensor)) {

                $arSensor = array(
                    'UF_ACTIVE' => true,
                    'UF_SYSTEM_ID' => $this->arSystem['ID'],
                    'UF_SENSOR_APP' => $obValue->SensorApp,
                    'UF_SENSOR_DEVICE' => $obValue->SensorClass,
                    'UF_SENSOR_NAME' => $obValue->SensorName,
                    'UF_SENSOR_UNIT' => $obValue->SensorUnit,
                );
                $rsResult = SensorsSensorsTable::add($arSensor);
                if ($rsResult->isSuccess()) {
                    $arSensor['ID'] = $rsResult->getId();
                } else {
                    $this->arResponse['errors'][] = 'Невозможно создать сенсор: ' . implode(';', $rsResult->getErrorMessages());
                    $this->arResponse['errors'][] = 'Данные: ' . print_r($arSensor, true);
                    continue;
                }
            } else {
                if (!$arSensor['UF_ACTIVE'])
                    continue;
            }

            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_AVG) {
                $arValue = $this->arTodayValues[$arSensor['ID']];

                if (!$arValue) {
                    $arValue = array(
                        'UF_SENSOR_ID' => $arSensor['ID'],
                        'UF_DATE' => new \Bitrix\Main\Type\Date(),
                        'UF_SENSOR_VALUE_MIN' => $value,
                        'UF_SENSOR_VALUE' => $value,
                        'UF_SENSOR_VALUE_MAX' => $value,
                        'UF_SENSOR_VALUES' => 1,
                    );
                    $rsResult = SensorsDataTable::add($arValue);
                    if (!$rsResult->isSuccess()) {
                        $this->arResponse['errors'][] = 'Невозможно добавить данные: ' . implode(';', $rsResult->getErrorMessages());
                        $this->arResponse['errors'][] = 'Данные: ' . print_r($arValue, true);
                    }
                } else {
                    if ($value < $arValue['UF_SENSOR_VALUE_MIN']) {
                        $arValue['UF_SENSOR_VALUE_MIN'] = $value;
                    }
                    if ($value > $arValue['UF_SENSOR_VALUE_MAX']) {
                        $arValue['UF_SENSOR_VALUE_MAX'] = $value;
                    }
                    $arValue['UF_SENSOR_VALUE'] = ($arValue['UF_SENSOR_VALUE'] * $arValue['UF_SENSOR_VALUES'] + $value) / ($arValue['UF_SENSOR_VALUES'] + 1);
                    $arValue['UF_SENSOR_VALUES'] ++;
                    $valueId = $arValue['ID'];
                    unset($arValue['ID']);
                    SensorsDataTable::update($valueId, $arValue);
                }
            }

            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_EACH) {
                $arValue = array(
                    'UF_SENSOR_ID' => $arSensor['ID'],
                    'UF_DATE' => new \Bitrix\Main\Type\DateTime(),
                    'UF_SENSOR_VALUE' => $value,
                );
                $rsResult = SensorsDataTable::add($arValue);
                if (!$rsResult->isSuccess()) {
                    $this->arResponse['errors'][] = 'Невозможно добавить данные: ' . implode(';', $rsResult->getErrorMessages());
                    $this->arResponse['errors'][] = 'Данные: ' . print_r($arValue, true);
                }
            }

            $this->checkAlert($arSensor, $value);
        }
    }

    /**
     * 
     * @param string $appName
     * @param string $deviceName
     * @param string $sensorName
     * @return array|null
     */
    private function getSensor(string $appName, string $deviceName, string $sensorName) {

        if (isset($this->arSensors
                [$appName]
                [$deviceName]
                [$sensorName])) {

            return $this->arSensors
                [$appName]
                [$deviceName]
                [$sensorName];
        }

        return null;
    }

    /**
     * 
     * @param array $arSensor
     * @param float $value
     */
    private function checkAlert(array $arSensor, float $value) {

        $isAlert = false;

        $message = 'Значение на датчике ' . $arSensor['UF_SENSOR_APP'] . ' > ' . $arSensor['UF_SENSOR_DEVICE'] . ' > ' . $arSensor['UF_SENSOR_NAME'] . ' = ' . $value . $arSensor['UF_SENSOR_UNIT'] . ' и ';

        if ($arSensor['UF_ALERT_VALUE_MIN'] != 0 && $value < $arSensor['UF_ALERT_VALUE_MIN']) {
            $message .= 'меньше допустимого ' . $arSensor['UF_ALERT_VALUE_MIN'];
            $isAlert = true;
        }

        if ($arSensor['UF_ALERT_VALUE_MAX'] != 0 && $value > $arSensor['UF_ALERT_VALUE_MAX']) {
            $message .= 'больше допустимого ' . $arSensor['UF_ALERT_VALUE_MAX'];
            $isAlert = true;
        }

        $message .= $arSensor['UF_ALERT_VALUE_MIN'] . $arSensor['UF_SENSOR_UNIT'];

        if ($isAlert) {
            $this->arResponse['alerts'][] = $message;
        }
    }

    private function sendAlerts() {
        if (count($this->arResponse['alerts']) > 0 && strlen($this->arSystem['UF_EMAIL']) > 0) {

            $message = 'Контроль сенсоров на системе ' . $this->arSystem['UF_NAME'] . '. Некоторые значения вне допустимого диапазона.<br><br>';
            $message .= implode('<br>', $this->arResponse['alerts']);

            \Bitrix\Main\Mail\Event::send(array(
                'EVENT_NAME' => 'SENSORS_ALERT',
                'LID' => 's1',
                'C_FIELDS' => array(
                    'EMAIL_TO' => $this->arSystem['UF_EMAIL'],
                    'SUBJECT' => 'Оповещение системы контроля сенсоров на системе ' . $this->arSystem['UF_NAME'],
                    'MESSAGE' => $message
                )
            ));
        }
    }

}
