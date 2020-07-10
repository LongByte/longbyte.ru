<?

namespace Api\Controller\Sensors;

/**
 * class \Api\Controller\Sensors\Edit
 */
class Edit extends \Api\Core\Base\Controller {

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
     * @param string|null $strToken
     */
    public function __construct($strToken = null) {
        parent::__construct();
        $this->token = $this->obRequest->get('token');
        if (!is_null($strToken)) {
            $this->token = $strToken;
        }
    }

    /**
     * 
     * @return string
     */
    public function get() {
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $this->arResponse['data'] = $this->obSystem->getSensorsCollection()->toArray();

        return $this->exitAction();
    }

    public function post() {
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $iSensorId = $this->getRequest()->get('id');
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        $obSensor = $this->obSystem->getSensorsCollection()->getByKey($iSensorId);

        if (!is_null($obSensor)) {
            $strAlertMuteTill = $this->getRequest()->get('alert_mute_till');
            if (strlen($strAlertMuteTill) <= 0) {
                $obAlertMuteTill = null;
            } else {
                $obAlertMuteTill = \Bitrix\Main\Type\DateTime::tryParse($strAlertMuteTill, 'd.m.Y H:i:s');
                if (!is_null($obAlertMuteTill)) {
                    if ($obAlertMuteTill->getTimestamp() < (new \Bitrix\Main\Type\DateTime())->getTimestamp()) {
                        $obAlertMuteTill = null;
                    }
                }
            }

            $obSensor
                ->setActive($this->getRequest()->get('active') == 1)
                ->setSensorUnit($this->getRequest()->get('sensor_unit'))
                ->setAlertValueMin($this->getRequest()->get('alert_value_min'))
                ->setAlertValueMax($this->getRequest()->get('alert_value_max'))
                ->setIgnoreLess($this->getRequest()->get('ignore_less'))
                ->setIgnoreMore($this->getRequest()->get('ignore_more'))
                ->setVisualMin($this->getRequest()->get('visual_min'))
                ->setVisualMax($this->getRequest()->get('visual_max'))
                ->setAlertEnable($this->getRequest()->get('alert_enable') == 1)
                ->setAlertMuteTill($obAlertMuteTill)
                ->setModifier($this->getRequest()->get('modifier'))
                ->setLogMode($this->getRequest()->get('log_mode'))
                ->setPrecision($this->getRequest()->get('precision'))
                ->setSort($this->getRequest()->get('sort'))
            ;
            if ($obSensor->isChanged()) {
                $obSensor->save();
            }
        }

        $this->loadSystem();

        $iSort = 0;
        foreach ($this->obSystem->getSensorsCollection() as $obSensor) {
            $iSort += 10;

            $obSensor->setSort($iSort);
            if ($obSensor->isChanged()) {
                $obSensor->save();
            }
        }

        $this->arResponse['data'] = $this->obSystem->getSensorsCollection()->toArray();

        return $this->exitAction();
    }

    public function delete() {
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $iSensorId = $this->getRequest()->get('id');
        $strMode = $this->getRequest()->get('mode');
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        $obSensor = $this->obSystem->getSensorsCollection()->getByKey($iSensorId);

        if (!is_null($obSensor)) {
            $arValuesFilter = array(
                'SENSOR_ID' => $obSensor->getId(),
                'SENSOR.SYSTEM_ID' => $this->obSystem->getId(),
            );

            if ($strMode == 'data' || $strMode == 'sensor') {
                /** @var \Api\Sensors\Data\Collection $obValues */
                $obValues = \Api\Sensors\Data\Model::getAll($arValuesFilter);
                /** @var \Api\Sensors\Data\Entity $obValue */
                foreach ($obValues as $obValue) {
                    $obValue->delete();
                }
            }

            if ($strMode == 'sensor') {
                $obSensor->delete();
            }
        }

        $this->loadSystem();

        $iSort = 0;
        foreach ($this->obSystem->getSensorsCollection() as $obSensor) {
            $iSort += 10;

            $obSensor->setSort($iSort);
            if ($obSensor->isChanged()) {
                $obSensor->save();
            }
        }

        $this->arResponse['data'] = $this->obSystem->getSensorsCollection()->toArray();

        return $this->exitAction();
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
    private function loadSystem() {
        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        $this->obSystem = \Api\Sensors\System\Model::getOne(array(
                '=TOKEN' => $this->token,
                'ACTIVE' => true
        ));

        if (!$this->obSystem) {
            $this->arResponse['success'] = false;
            $this->arResponse['errors'][] = 'Неверный токен';

            return false;
        }

        $obSensors = \Api\Sensors\Sensor\Model::getAll(
                array('SYSTEM_ID' => $this->obSystem->getId()),
                0,
                0,
                array('order' => array('SORT' => 'ASC'))
        );

        $this->obSystem->setSensorsCollection($obSensors);

        return true;
    }

}
