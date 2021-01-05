<?

namespace Api\Controller\Sensors;

/**
 * class \Api\Controller\Sensors\Edit
 */
class Edit extends \Api\Core\Base\Controller {

    /**
     *
     * @var array
     */
    protected $arResponse = array(
        'data' => array(),
        'errors' => array(),
        'success' => true,
    );

    /**
     *
     * @var \Api\Sensors\System\Entity
     */
    protected $obSystem = null;

    /**
     * 
     * @param string|null $strToken
     */
    public function __construct($strToken = null) {
        parent::__construct();
        $this->token = $this->getRequest()->get('token');
        if (!is_null($strToken)) {
            $this->token = $strToken;
        }
    }

    /**
     * 
     * @return mixed
     */
    public function get() {
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }
        
        $this->getStatistic();

        $this->arResponse['data'] = array(
            'system' => $this->getSystem()->toArray(),
            'sensors' => $this->getSystem()->getSensorsCollection()->toArray(),
            'links' => $this->getLinks(),
        );


        return $this->exitAction();
    }

    /**
     * 
     * @return mixed
     */
    public function post() {
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $iSensorId = $this->getRequest()->get('id');
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        $obSensor = $this->getSystem()->getSensorsCollection()->getByKey($iSensorId);

        if (!is_null($obSensor)) {


            $arFields = array(
                'active',
                'sensor_unit',
                'alert_value_min',
                'alert_value_max',
                'ignore_less',
                'ignore_more',
                'visual_min',
                'visual_max',
                'alert_enable',
                'alert_mute_till',
                'modifier',
                'log_mode',
                'precision',
                'sort',
                'label',
            );

            foreach ($arFields as $strField) {
                if (is_null($this->getRequest()->get($strField))) {
                    continue;
                }

                switch ($strField) {
                    case 'alert_mute_till':
                        $strAlertMuteTill = $this->getRequest()->get($strField);
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
                        $obSensor->set(strtoupper($strField), $obAlertMuteTill);
                        break;
                    case 'active':
                    case 'alert_enable':
                        $obSensor->set(strtoupper($strField), $this->getRequest()->get($strField) == 1);
                        break;
                    default :
                        $obSensor->set(strtoupper($strField), $this->getRequest()->get($strField));
                        break;
                }
            }


            if ($obSensor->isChanged()) {
                $obSensor->save();
            }
        }

        $this->loadSystem();

        $iSort = 0;
        foreach ($this->getSystem()->getSensorsCollection() as $obSensor) {
            $iSort += 10;

            $obSensor->setSort($iSort);
            if ($obSensor->isChanged()) {
                $obSensor->save();
            }
        }
        
        $this->getStatistic();

        $this->arResponse['data'] = array(
            'system' => $this->getSystem()->toArray(),
            'sensors' => $this->getSystem()->getSensorsCollection()->toArray(),
            'links' => $this->getLinks(),
        );

        return $this->exitAction();
    }

    public function delete() {
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $iSensorId = $this->getRequest()->get('id');
        $strMode = $this->getRequest()->get('mode');
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        $obSensor = $this->getSystem()->getSensorsCollection()->getByKey($iSensorId);

        if (!is_null($obSensor)) {
            $arValuesFilter = array(
                'SENSOR_ID' => $obSensor->getId(),
                'SENSOR.SYSTEM_ID' => $this->getSystem()->getId(),
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
        foreach ($this->getSystem()->getSensorsCollection() as $obSensor) {
            $iSort += 10;

            $obSensor->setSort($iSort);
            if ($obSensor->isChanged()) {
                $obSensor->save();
            }
        }

        $this->arResponse['data'] = $this->getSystem()->getSensorsCollection()->toArray();

        return $this->exitAction();
    }

    /**
     * 
     * @return string
     */
    protected function exitAction(): string {
        header('Content-Type: application/json');
        return json_encode($this->arResponse);
    }

    /**
     * 
     * @return bool
     */
    protected function loadSystem(): bool {
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

    /**
     * 
     * @return \Api\Sensors\System\Entity|null
     */
    protected function getSystem(): ?\Api\Sensors\System\Entity {
        return $this->obSystem;
    }

    /**
     * 
     * @return array
     */
    protected function getLinks(): array {
        $arLinks = array(
            array(
                'href' => \Api\Sensors\Links::getInstance()->getSystemUrl($this->getSystem()->getNameToken()),
                'title' => 'Текущая статистика'
            ),
            array(
                'href' => \Api\Sensors\Links::getInstance()->getStatUrl($this->getSystem()->getNameToken()),
                'title' => 'Статистика за все время'
            ),
        );

        return $arLinks;
    }

    /**
     * 
     */
    protected function getStatistic() {

        $obQuery = new \Bitrix\Main\ORM\Query\Query(\Api\Sensors\Data\Table::getEntity());
        $obQuery
            ->where('SENSOR.SYSTEM_ID', '=', $this->obSystem->getId())
            ->setGroup('SENSOR_ID')
            ->registerRuntimeField(
                'TOTAL_MIN',
                new \Bitrix\Main\ORM\Fields\ExpressionField(
                    'TOTAL_MIN',
                    'MIN(%s)',
                    array('VALUE_MIN')
                )
            )
            ->registerRuntimeField(
                'TOTAL_MAX',
                new \Bitrix\Main\ORM\Fields\ExpressionField(
                    'TOTAL_MAX',
                    'MAX(%s)',
                    array('VALUE_MAX')
                )
            )
            ->registerRuntimeField(
                'DATA_COUNT',
                new \Bitrix\Main\ORM\Fields\ExpressionField(
                    'DATA_COUNT',
                    'COUNT(%s)',
                    array('ID')
                )
            )
            ->setSelect(array('SENSOR_ID', 'TOTAL_MIN', 'TOTAL_MAX', 'DATA_COUNT'))
        ;

        $rsResult = $obQuery->exec();
        while ($arData = $rsResult->fetch()) {
            $obStatistic = new \Api\Sensors\Sensor\Statistic\Entity();
            $obStatistic
                ->setValueMin($arData['TOTAL_MIN'])
                ->setValueMax($arData['TOTAL_MAX'])
                ->setValuesCount($arData['DATA_COUNT'])
            ;
            /** @var \Api\Sensors\Sensor\Entity $obSensor */
            if ($obSensor = $this->getSystem()->getSensorsCollection()->getByKey($arData['SENSOR_ID'])) {
                $obSensor->setStatistic($obStatistic);
            }
        }
    }

}
