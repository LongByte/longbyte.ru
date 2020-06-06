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
        if (!$this->getSystem()) {
            return $this->exitAction();
        }

        $this->arResponse['data'] = $this->obSystem->getSensorsCollection()->toArray();

        return $this->exitAction();
    }

    public function post() {
        if (!$this->getSystem()) {
            return $this->exitAction();
        }

        $iSensorId = $this->getRequest()->get('id');
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        $obSensor = $this->obSystem->getSensorsCollection()->getByKey($iSensorId);

        if (!is_null($obSensor)) {
//            $offAlert = $this->getRequest()->get('off_alert');
//            if (strlen($offAlert) <= 0) {
//                $offAlert = null;
//            } else {
//                $offAlert = new \Bitrix\Main\Type\DateTime();
//            }

            $obSensor
                ->setActive($this->getRequest()->get('active') == 1)
                ->setAlertValueMin($this->getRequest()->get('alert_value_min'))
                ->setAlertValueMax($this->getRequest()->get('alert_value_max'))
                ->setIgnoreLess($this->getRequest()->get('ignore_less'))
                ->setIgnoreMore($this->getRequest()->get('ignore_more'))
                ->setVisualMin($this->getRequest()->get('visual_min'))
                ->setVisualMax($this->getRequest()->get('visual_max'))
                ->save()
            ;
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
    private function getSystem() {
        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        if (is_null($this->obSystem)) {
            $this->obSystem = \Api\Sensors\System\Model::getOne(array(
                    '=TOKEN' => $this->token,
                    'ACTIVE' => true
            ));
        }

        if ($this->obSystem) {
            if ($this->obSystem->getSensorsCollection()->count() <= 0) {

                $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                        'SYSTEM_ID' => $this->obSystem->getId(),
                ));

                $this->obSystem->setSensorsCollection($obSensors);
            }

            return true;
        } else {
            $this->arResponse['success'] = false;
            $this->arResponse['errors'][] = 'Неверный токен';
        }

        return false;
    }

}
