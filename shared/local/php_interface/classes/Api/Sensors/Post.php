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
        $arSystem = \Api\Sensors\System\Table::getRow(array(
                'filter' => array(
                    '=TOKEN' => $this->token,
                    'ACTIVE' => true
                ),
        ));

        if ($arSystem) {
            $this->arSystem = $arSystem;

            $rsSensors = \Api\Sensors\Sensor\Table::getList(array(
                    'filter' => array(
                        'SYSTEM_ID' => $this->arSystem ['ID']
                    ),
            ));

            while ($arSensor = $rsSensors->fetch()) {
                $this->arSensors
                    [$arSensor['SENSOR_APP']]
                    [$arSensor['SENSOR_DEVICE']]
                    [$arSensor['SENSOR_NAME']] = $arSensor;
            }

            if ($this->arSystem['MODE'] == \Api\Sensors\System\Table::MODE_AVG) {
                $rsValues = \Api\Sensors\Data\Table::getList(array(
                        'filter' => array(
                            'SENSOR.SYSTEM_ID' => $this->arSystem['ID'],
                            'DATE' => new \Bitrix\Main\Type\Date(),
                        ),
                ));

                while ($arValue = $rsValues->fetch()) {
                    $this->arTodayValues
                        [$arValue['SENSOR_ID']] = $arValue;
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
                    'ACTIVE' => true,
                    'SYSTEM_ID' => $this->arSystem['ID'],
                    'SENSOR_APP' => $obValue->SensorApp,
                    'SENSOR_DEVICE' => $obValue->SensorClass,
                    'SENSOR_NAME' => $obValue->SensorName,
                    'SENSOR_UNIT' => $obValue->SensorUnit,
                );
                $rsResult = \Api\Sensors\Sensor\Table::add($arSensor);
                if ($rsResult->isSuccess()) {
                    $arSensor['ID'] = $rsResult->getId();
                } else {
                    $this->arResponse['errors'][] = 'Невозможно создать сенсор: ' . implode(';', $rsResult->getErrorMessages());
                    $this->arResponse['errors'][] = 'Данные: ' . print_r($arSensor, true);
                    continue;
                }
            } else {
                if (!$arSensor['ACTIVE'])
                    continue;
            }

            if ($this->arSystem['MODE'] == \Api\Sensors\System\Table::MODE_AVG) {
                $arValue = $this->arTodayValues[$arSensor['ID']];

                if (!$arValue) {
                    $arValue = array(
                        'SENSOR_ID' => $arSensor['ID'],
                        'DATE' => new \Bitrix\Main\Type\Date(),
                        'SENSOR_VALUE_MIN' => $value,
                        'SENSOR_VALUE' => $value,
                        'SENSOR_VALUE_MAX' => $value,
                        'SENSOR_VALUES' => 1,
                    );
                    $rsResult = \Api\Sensors\Data\Table::add($arValue);
                    if (!$rsResult->isSuccess()) {
                        $this->arResponse['errors'][] = 'Невозможно добавить данные: ' . implode(';', $rsResult->getErrorMessages());
                        $this->arResponse['errors'][] = 'Данные: ' . print_r($arValue, true);
                    }
                } else {
                    if ($value < $arValue['SENSOR_VALUE_MIN']) {
                        $arValue['SENSOR_VALUE_MIN'] = $value;
                    }
                    if ($value > $arValue['SENSOR_VALUE_MAX']) {
                        $arValue['SENSOR_VALUE_MAX'] = $value;
                    }
                    $arValue['SENSOR_VALUE'] = ($arValue['SENSOR_VALUE'] * $arValue['SENSOR_VALUES'] + $value) / ($arValue['SENSOR_VALUES'] + 1);
                    $arValue['SENSOR_VALUES'] ++;
                    $valueId = $arValue['ID'];
                    unset($arValue['ID']);
                    \Api\Sensors\Data\Table::update($valueId, $arValue);
                }
            }

            if ($this->arSystem['MODE'] == \Api\Sensors\System\Table::MODE_EACH) {
                $arValue = array(
                    'SENSOR_ID' => $arSensor['ID'],
                    'DATE' => new \Bitrix\Main\Type\DateTime(),
                    'SENSOR_VALUE' => $value,
                );
                $rsResult = \Api\Sensors\Data\Table::add($arValue);
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

        $message = 'Значение на датчике ' . $arSensor['SENSOR_APP'] . ' > ' . $arSensor['SENSOR_DEVICE'] . ' > ' . $arSensor['SENSOR_NAME'] . ' = ' . $value . $arSensor['SENSOR_UNIT'] . ' и ';

        if ($arSensor['ALERT_VALUE_MIN'] != 0 && $value < $arSensor['ALERT_VALUE_MIN']) {
            $message .= 'меньше допустимого ' . $arSensor['ALERT_VALUE_MIN'];
            $isAlert = true;
        }

        if ($arSensor['ALERT_VALUE_MAX'] != 0 && $value > $arSensor['ALERT_VALUE_MAX']) {
            $message .= 'больше допустимого ' . $arSensor['ALERT_VALUE_MAX'];
            $isAlert = true;
        }

        $message .= $arSensor['SENSOR_UNIT'];

        if ($isAlert) {
            $this->arResponse['alerts'][] = $message;
        }
    }

    /**
     * 
     */
    private function sendAlerts() {
        if (count($this->arResponse['alerts']) > 0 && strlen($this->arSystem['EMAIL']) > 0) {

            $strUrl = 'https://longbyte.ru/sensors/' . $this->arSystem['NAME'] . '-' . $this->arSystem['TOKEN'] . '/';

            $message = 'Контроль сенсоров на системе <a href="' . $strUrl . '">' . $this->arSystem['NAME'] . '</a>. Некоторые значения вне допустимого диапазона.<br><br>';
            $message .= implode('<br>', $this->arResponse['alerts']);

            \Bitrix\Main\Mail\Event::send(array(
                'EVENT_NAME' => 'SENSORS_ALERT',
                'LID' => 's1',
                'C_FIELDS' => array(
                    'EMAIL_TO' => $this->arSystem['EMAIL'],
                    'SUBJECT' => 'Оповещение системы контроля сенсоров на системе ' . $this->arSystem['NAME'],
                    'MESSAGE' => $message
                )
            ));
        }
    }

}
