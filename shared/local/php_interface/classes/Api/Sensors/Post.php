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
    private $systemId = null;

    public function __construct() {
        $this->obRequest = Context::getCurrent()->getRequest();
        $this->rawPost = file_get_contents('php://input');
        $this->token = $this->obRequest->get('token');
    }

    public function post() {
        $arData = json_decode($this->rawPost);
        $this->getSystem();
        $this->insertSensorsData($arData);
    }

    public function get() {
        $obHttp = new \Bitrix\Main\Web\HttpClient();
        $rawGet = $obHttp->get('http://localhost:55555/');
        $arData = json_decode($rawGet);
        $this->getSystem();
        $this->insertSensorsData($arData);
    }

    private function getSystem() {
        $arSystem = SensorsSystemTable::getRow(array(
                'filter' => array('=UF_TOKEN' => $this->token),
        ));

        if ($arSystem) {
            $this->systemId = $arSystem['ID'];
        }
    }

    private function insertSensorsData(array $arData) {

        $arSensors = array();
        $rsSensors = SensorsDataTable::getList(array(
                'filter' => array(
                    'UF_SYSTEM_ID' => $this->systemId,
                    'UF_DATE' => new \Bitrix\Main\Type\Date(),
                ),
        ));

        while ($arSensor = $rsSensors->fetch()) {
            $arSensors
                [$arSensor['UF_SENSOR_APP']]
                [$arSensor['UF_SENSOR_DEVICE']]
                [$arSensor['UF_SENSOR_NAME']] = $arSensor;
        }

        foreach ($arData as $obNewSensorData) {

            $value = floatval(str_replace(',', '.', $obNewSensorData->SensorValue));

            $arSensor = $arSensors
                [$obNewSensorData->SensorApp]
                [$obNewSensorData->SensorClass]
                [$obNewSensorData->SensorName]
            ;

            if (!$arSensor) {
                $arSensor = array(
                    'UF_SYSTEM_ID' => $this->systemId,
                    'UF_DATE' => new \Bitrix\Main\Type\Date(),
                    'UF_SENSOR_APP' => $obNewSensorData->SensorApp,
                    'UF_SENSOR_DEVICE' => $obNewSensorData->SensorClass,
                    'UF_SENSOR_NAME' => $obNewSensorData->SensorName,
                    'UF_SENSOR_VALUE_MIN' => $value,
                    'UF_SENSOR_VALUE_AVG' => $value,
                    'UF_SENSOR_VALUE_MAX' => $value,
                    'UF_SENSOR_VALUES' => 1,
                    'UF_SENSOR_UNIT' => $obNewSensorData->SensorUnit,
                );
                $rsResult = SensorsDataTable::add($arSensor);
                if (!$rsResult->isSuccess()) {
                    echo '<pre>';
                    print_r($rsResult->getErrorMessages());
                    print_r($arSensor);
                    echo '</pre>';
                }
            } else {
                if ($value < $arSensor['UF_SENSOR_VALUE_MIN']) {
                    $arSensor['UF_SENSOR_VALUE_MIN'] = $value;
                }
                if ($value > $arSensor['UF_SENSOR_VALUE_MAX']) {
                    $arSensor['UF_SENSOR_VALUE_MAX'] = $value;
                }
                $arSensor['UF_SENSOR_VALUE_MAX'] = ($arSensor['UF_SENSOR_VALUE_AVG'] * $arSensor['UF_SENSOR_VALUES'] + $value) / ($arSensor['UF_SENSOR_VALUES'] + 1);
                $arSensor['UF_SENSOR_VALUES'] ++;
                $sensorRowId = $arSensor['ID'];
                unset($arSensor['ID']);
                SensorsDataTable::update($sensorRowId, $arSensor);
            }
        }
    }

}
