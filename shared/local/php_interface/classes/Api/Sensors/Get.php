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
    private $arResponse = array(
        'data' => array(),
        'errors' => array(),
        'alerts' => array(),
        'success' => true,
    );
    private $arSystem = null;

    public function __construct() {
        $this->obRequest = Context::getCurrent()->getRequest();
        $this->token = $this->obRequest->get('token');
    }

    public function get() {

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

        $arSystem = $this->arSystem;
        $this->clearUF($arSystem);

        $arSensors = array();
        $rsSensors = SensorsSensorsTable::getList(array(
                'filter' => array(
                    'UF_SYSTEM_ID' => $this->arSystem['ID'],
                    'UF_ACTIVE' => true,
                ),
        ));


        while ($arSensor = $rsSensors->fetch()) {
            $this->clearUF($arSensor);
            $arSensor['VALUES'] = array();
            $arSensors[$arSensor['ID']] = $arSensor;
        }

        $arValues = array();
        $rsValues = SensorsDataTable::getList(array(
                'filter' => array(
                    'SENSOR.UF_ACTIVE' => true,
                    'SENSOR.UF_SYSTEM_ID' => $this->arSystem['ID'],
                    '>=UF_DATE' => $obDate,
                    '<UF_DATE' => $obDateTo,
                ),
        ));

        while ($arValue = $rsValues->fetch()) {
            $this->clearUF($arValue);
            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_AVG) {
                $arValue['DATE'] = $arValue['DATE']->format('d.m.Y');
            }
            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_EACH) {
                $arValue['DATE'] = $arValue['DATE']->format('d.m.Y H:i:s');
            }
            $arSensors[$arValue['SENSOR_ID']]['VALUES'][] = $arValue;
        }

        $arVue = array(
            'SYSTEM' => $arSystem,
            'SENSORS' => $arSensors,
            'DATE' => $obDate->format('d.m.Y'),
        );

        \LongByte\Vue::arrayKeyToLower($arVue);

        $this->arResponse['data'] = $arVue;

        return $this->exitAction();
    }

    private function clearUF(array &$array) {
        foreach ($array as $key => $value) {
            if (strpos($key, 'UF_') === 0) {
                unset($array[$key]);
                $key = str_replace('UF_', '', $key);
                $array[$key] = $value;
            }
        }
    }

    /**
     * 
     * @return string
     */
    private function exitAction() {
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

}
