<?

namespace Api\Controller\Sensors;

/**
 * Class \Api\Controller\Sensors\Stat
 */
class Stat extends \Api\Core\Base\Controller
{

    private ?string $token = null;

    private array $arResponse = array(
        'data' => array(),
        'errors' => array(),
        'success' => true,
    );

    private ?\Api\Sensors\System\Entity $obSystem = null;

    public function __construct()
    {
        parent::__construct();
        $this->token = $this->getRequest()->get('token');
    }

    public function get()
    {
        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $obToday = new \Bitrix\Main\Type\Date();

        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
            'SYSTEM_ID' => $this->getSystem()->getId(),
            'ACTIVE' => true,
        ), 0, 0, array(
            'order' => array('SORT' => 'ASC')
        ));

        $arValuesFilter = array(
            'SENSOR.ACTIVE' => true,
            'SENSOR.SYSTEM_ID' => $this->getSystem()->getId(),
            '<DATE' => (new \Bitrix\Main\Type\Date()),
            '>VALUES_COUNT' => 0
        );

        $strSince = $this->getRequest()->get('since');
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

            if (!$this->getSystem()->getSensorsCollection()->getByKey($obSensor->getId())) {
                $this->getSystem()->getSensorsCollection()->addItem($obSensor);
            }
            $obSensor->setSystem($this->getSystem());

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
            'system' => $this->getSystem()->toArray(),
            'sensors' => $obSensors->toArray(),
            'links' => $this->getLinks(),
        );

        $this->arResponse['data'] = $arVue;

        return $this->exitAction();
    }

    protected function exitAction(): string
    {
        $this->arrayValueToNumber($this->arResponse);
        header('Content-Type: application/json');
        return json_encode($this->arResponse);
    }

    private function arrayValueToNumber(array &$array)
    {
        foreach ($array as $key => &$value) {
            if (is_array($value)) {
                self::arrayValueToNumber($value);
            }
            if (is_numeric($value)) {
                $value = round($value, 3);
            }
        }
    }

    private function loadSystem(): bool
    {

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

    private function getSystem(): ?\Api\Sensors\System\Entity
    {
        return $this->obSystem;
    }

    private function getLinks(): array
    {
        $arLinks = array(
            array(
                'href' => \Api\Sensors\Links::getInstance()->getSystemUrl($this->getSystem()->getNameToken()),
                'title' => 'Текущая статистика'
            ),
            array(
                'href' => \Api\Sensors\Links::getInstance()->getEditUrl($this->getSystem()->getNameToken()),
                'title' => 'Настроить датчики'
            ),
            array(
                'href' => \Api\Sensors\Links::getInstance()->getStatUrl($this->getSystem()->getNameToken()),
                'title' => 'Статистика за все время'
            ),
            array(
                'href' => \Api\Sensors\Links::getInstance()->getStatSinceUrl($this->getSystem()->getNameToken(), '-1month'),
                'title' => 'за месяц'
            ),
            array(
                'href' => \Api\Sensors\Links::getInstance()->getStatSinceUrl($this->getSystem()->getNameToken(), '-6months'),
                'title' => 'за пол года'
            ),
        );

        return $arLinks;
    }

}
