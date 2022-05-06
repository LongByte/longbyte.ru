<?

namespace Api\Controller\Sensors;

use Bitrix\Main\Type\DateTime;

/**
 * Class \Api\Controller\Sensors\Get
 */
class Get extends \Api\Core\Base\Controller
{

    private ?string $token = null;
    private ?string $name = null;
    private array $arResponse = array(
        'data' => array(),
        'errors' => array(),
        'alerts' => array(),
        'success' => true,
    );

    private ?\Api\Sensors\System\Entity $obSystem = null;

    public function __construct()
    {
        parent::__construct();
        $this->name = $this->getRequest()->get('name');
        $this->token = $this->getRequest()->get('token');
    }

    public function get(): string
    {

        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $obToday = new DateTime();
        $obToday->setTime(0, 0, 0);

        $date = $this->getRequest()->get('date');
        $strSince = $this->getRequest()->get('since');

        if (strlen($date) > 0) {
            $obDate = new DateTime($date, 'd.m.Y');
        } else {
            $obDate = new DateTime();
        }
        $obDate->setTime(0, 0, 0);

        $bToday = $obToday->getTimestamp() == $obDate->getTimestamp();

        $obDateTo = clone $obDate;
        $obDateTo->add('+1day');

        if (strlen($strSince) > 0) {
            $obDate = (new DateTime())->add('-1hour');
        }

        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
            'SYSTEM_ID' => $this->getSystem()->getId(),
            'ACTIVE' => true,
        ), 0, 0, array(
            'order' => array('SORT' => 'ASC')
        ));

        $arValuesFilter = array(
            'SENSOR.ACTIVE' => true,
            'SENSOR.SYSTEM_ID' => $this->getSystem()->getId(),
            '>=DATE' => $obDate,
            '<DATE' => $obDateTo,
        );

        $obValues = \Api\Sensors\Data\Model::getAll($arValuesFilter, 0, 0, array(
            'order' => array('DATE' => 'ASC')
        ));

        foreach ($obValues as $obValue) {
            $obSensor = $obSensors->getByKey($obValue->getSensorId());
            $obSensor->setToday($bToday);

            $this->getSystem()->getSensorsCollection()->addItem($obSensor);
            $obSensor->setSystem($this->getSystem());

            $obSensor->getValuesCollection()->addItem($obValue);
            $obValue->setSensor($obSensor);

            $valueMin = $obValue->getValueMin();
            $valueMax = $obValue->getValueMax();

            if ($obSensor->getAlertValueMax() != 0 && $valueMax > $obSensor->getAlertValueMax()) {
                $obSensor->getAlert()->setAlert(true);
                $obSensor->getAlert()->setDirection(1);
            }

            if ($obSensor->getAlertValueMin() != 0 && $valueMin < $obSensor->getAlertValueMin()) {
                $obSensor->getAlert()->setAlert(true);
                $obSensor->getAlert()->setDirection(-1);
            }
        }

        $arVue = array(
            'system' => $this->getSystem()->toArray(),
            'sensors' => $obSensors->toArray(),
            'date' => $obDate->format('d.m.Y'),
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

    private function arrayValueToNumber(array &$array): void
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
            '=NAME' => $this->name,
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
                'href' => \Api\Sensors\Links::getInstance()->getEditUrl($this->getSystem()->getNameToken()),
                'title' => 'Настроить датчики'
            ),
            array(
                'href' => \Api\Sensors\Links::getInstance()->getStatUrl($this->getSystem()->getNameToken()),
                'title' => 'Статистика за все время'
            ),
        );

        return $arLinks;
    }

}
