<?

namespace Api\Controller\Sensors;

/**
 * class \Api\Controller\Sensors\Stat
 */
class Stat extends \Api\Core\Base\Controller {

    /**
     *
     * @var string
     */
    private $token = null;

    /**
     *
     * @var array
     */
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

    public function __construct() {
        parent::__construct();
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

        $obToday = new \Bitrix\Main\Type\Date();

        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                'SYSTEM_ID' => $this->obSystem->getId(),
                'ACTIVE' => true,
                ), 0, 0, array(
                'order' => array('SORT' => 'ASC')
        ));

        $arValuesFilter = array(
            'SENSOR.ACTIVE' => true,
            'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
            '<DATE' => (new \Bitrix\Main\Type\Date()),
            '>VALUES_COUNT' => 0
        );

        $strSince = $this->obRequest->get('since');
        if (strlen($strSince) > 0) {
            $obSince = new \Bitrix\Main\Type\Date();
            $obSince->add($strSince);
            $arValuesFilter['>=DATE'] = $obSince;
        }

        $obValues = \Api\Sensors\Data\Model::getAll($arValuesFilter, 0, 0, array(
                'order' => array('DATE' => 'ASC')
        ));

        foreach ($obValues as $obValue) {
            $obSensor = $obSensors->getByKey($obValue->getSensorId());
            if ($obSensor->getValuesCollection()->getByDateAndSensorId($obValue->getDate(), $obValue->getSensorId())) {
                continue;
            }

            if (!$this->obSystem->getSensorsCollection()->getByKey($obSensor->getId())) {
                $this->obSystem->getSensorsCollection()->addItem($obSensor);
            }
            $obSensor->setSystem($this->obSystem);

            $obSensor->getValuesCollection()->addItem($obValue);
            $obValue->setSensor($obSensor);

            $obDiff = $obToday->getDiff($obValue->getDate());
            if ($obDiff->days < 10) {
                if ($obSensor->getAlertValueMax() != 0 && $obValue->getValueMax() > $obSensor->getAlertValueMax()) {
                    $obSensor->getAlert()->setAlert(true);
                    $obSensor->getAlert()->setDirection(1);
                }

                if ($obSensor->getAlertValueMin() != 0 && $obValue->getValueMin() < $obSensor->getAlertValueMin()) {
                    $obSensor->getAlert()->setAlert(true);
                    $obSensor->getAlert()->setDirection(-1);
                }
            }
        }

        $arVue = array(
            'system' => $this->obSystem->toArray(),
            'sensors' => $obSensors->toArray(),
        );

        $this->arResponse['data'] = $arVue;

        return $this->exitAction();
    }

    /**
     * 
     * @return string
     */
    private function exitAction(): string {
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
     * @return bool
     */
    private function getSystem(): bool {

        $this->obSystem = \Api\Sensors\System\Model::getOne(array(
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
