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
            $arSensor['ALERT'] = false;
            $arSensor['ALERT_DIRECTION'] = 0;
            $arSensors[$arSensor['ID']] = $arSensor;
        }

        $arValues = array();
        $arValuesFilter = array(
            'SENSOR.UF_ACTIVE' => true,
            'SENSOR.UF_SYSTEM_ID' => $this->arSystem['ID'],
        );
        if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_AVG) {
            $arValuesFilter['UF_DATE'] = $obDate;
        }
        if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_EACH) {
            $arValuesFilter['>=UF_DATE'] = $obDate;
            $arValuesFilter['<UF_DATE'] = $obDateTo;
        }
        $rsValues = SensorsDataTable::getList(array(
                'filter' => $arValuesFilter,
        ));

        while ($arValue = $rsValues->fetch()) {
            $this->clearUF($arValue);
            $arSensor = &$arSensors[$arValue['SENSOR_ID']];

            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_AVG) {
                $arValue['DATE'] = $arValue['DATE']->format('d.m.Y');
            }
            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_EACH) {
                $arValue['DATE'] = $arValue['DATE']->format('H:i');
            }

            $valueMin = 0;
            $valueMax = 0;
            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_AVG) {
                $valueMin = $arValue['SENSOR_VALUE_MIN'];
                $valueMax = $arValue['SENSOR_VALUE_MAX'];
            }
            if ($this->arSystem['UF_MODE'] == SensorsSystemTable::MODE_EACH) {
                $valueMin = $arValue['SENSOR_VALUE'];
                $valueMax = $arValue['SENSOR_VALUE'];
            }

            if (!$arSensor['ALERT'] && $arSensor['ALERT_VALUE_MAX'] != 0 && $valueMax > $arSensor['ALERT_VALUE_MAX']) {
                $arSensor['ALERT'] = true;
                $arSensor['ALERT_DIRECTION'] = 1;
            }

            if (!$arSensor['ALERT'] && $arSensor['ALERT_VALUE_MIN'] != 0 && $valueMin < $arSensor['ALERT_VALUE_MIN']) {
                $arSensor['ALERT'] = true;
                $arSensor['ALERT_DIRECTION'] = 1;
            }

            $arSensor['VALUES'][] = $arValue;
        }
        unset($arSensor);

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
