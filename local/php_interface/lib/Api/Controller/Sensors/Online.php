<?

namespace Api\Controller\Sensors;

use Bitrix\Main\Type\DateTime;

/**
 * Class \Api\Controller\Sensors\Online
 */
class Online extends \Api\Core\Base\Controller
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

    public function get()
    {

        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        if (!$this->loadSystem()) {
            return $this->exitAction();
        }

        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
            'SYSTEM_ID' => $this->getSystem()->getId(),
            'ACTIVE' => true,
        ), 0, 0, array(
            'order' => array('SORT' => 'ASC')
        ));

        $this->getSystem()->setSensorsCollection($obSensors);

        $remoteSensorsSocket = 'longbyte.ru';
        $remotePort = 56999;

        $remoteSensorsSocketIP = gethostbyname($remoteSensorsSocket);

        $obSocket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($obSocket === false) {
            $this->arResponse['errors'][] = "Не удалось выполнить socket_create(): причина: " . socket_last_error($obSocket) . "\n";
        } else {

            $this->arResponse['errors'][] = "Пытаемся соединиться с '$remoteSensorsSocketIP' на порту '$remotePort'...";
            $result = socket_connect($obSocket, $remoteSensorsSocketIP, $remotePort);
            if ($result === false) {
                $this->arResponse['errors'][] = "Не удалось выполнить socket_connect().\nПричина: ($result) " . socket_last_error($obSocket) . "\n";
            } else {

                $arData = array(
                    'token' => $this->token,
                    'command' => 'getDebug',
                );
                $jsonData = json_encode($arData);
                $dataLendth = strlen($jsonData);
                socket_write($obSocket, $jsonData, $dataLendth);
                $rawMessage = socket_read($obSocket, 1024 * 1024);
                $dataLendth = strlen($rawMessage);
                $rawMessage = trim($rawMessage);

                if (strlen($rawMessage) > 0) {
                    $arMessage = json_decode($rawMessage, true);
                    $arMessage['post_data'] = json_decode($arMessage['post_data'], true);

                    $this->insertSensorsData($arMessage['post_data']);
                }
                if (socket_last_error($obSocket)) {
                    $this->arResponse['errors'][] = "Ошибка сокета.\nПричина: " . socket_last_error($obSocket) . "\n";
                    socket_close($obSocket);
                }
            }
        }


        $arVue = array(
            'system' => $this->getSystem()->toArray(),
            'sensors' => $obSensors->toArray(),
        );

        $this->arResponse['data'] = $arVue;

        return $this->exitAction();
    }

    private function insertSensorsData(array $arData): void
    {

        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        /** @var \Api\Sensors\Data\Collection $obValues */
        /** @var \Api\Sensors\Data\Entity $obValue */
        foreach ($arData as $arInputValue) {

            $this->arResponse['data']['read_values']++;
            $value = floatval(str_replace(',', '.', $arInputValue['SensorValue']));

            $obSensor = $this->getSystem()->getSensorsCollection()->getByParams($arInputValue['SensorApp'], $arInputValue['SensorClass'], $arInputValue['SensorName']);

            if (is_null($obSensor)) {
                continue;
            } else {
                if (!$obSensor->getActive()) {
                    continue;
                }
            }

            try {
                if (strlen($obSensor->getModifier()) > 0) {
                    $strModifier = $obSensor->getModifier();
                    $strModifier = preg_replace('/[^\d\+\*\/\-\.]/', '', $strModifier);
                    if (preg_match('/^[\+\*\/\-]\d+(\.\d+)?([\+\*\/\-]\d+(\.\d+)?)?$/', $strModifier)) {
                        $strModifier = preg_replace('/^([\+\*\/\-]\d+(\.\d+)?)(([\+\*\/\-]\d+(\.\d+)?)?)$/', '$1)$3', $strModifier);
                        $fModifiedValue = @eval('return ($value' . $strModifier . ';');
                        if (is_numeric($fModifiedValue)) {
                            $value = $fModifiedValue;
                        }
                    }
                }
            } catch (\ParseError $exc) {

            }

            $value = round($value, (int) $obSensor->getPrecision());

            $obDate = new DateTime();

            $obValue = new \Api\Sensors\Data\Entity();
            $obValue
                ->setSensor($obSensor)
                ->setDate($obDate)
                ->setValueMin($value)
                ->setValueAvg($value)
                ->setValueMax($value)
                ->setValue($value)
                ->setValuesCount(1)
            ;

            $obSensor->getValuesCollection()->addItem($obValue);

            $this->checkAlert($obValue);
        }
    }

    private function checkAlert(\Api\Sensors\Data\Entity $obValue)
    {

        $obSensor = $obValue->getSensor();

        if ($obSensor->getAlertValueMin() != 0 && $obValue->getValue() < $obSensor->getAlertValueMin()) {
            if ($obSensor->getAlert()->getValueMin() == 0 || $obValue->getValue() < $obSensor->getAlert()->getValueMin()) {
                $obSensor->getAlert()->setAlert(true);
                $obSensor->getAlert()->setDirection(-1);
                $obSensor->getAlert()->setValueMin($obValue->getValue());
            }
        }

        if ($obSensor->getAlertValueMax() != 0 && $obValue->getValue() > $obSensor->getAlertValueMax()) {
            if ($obSensor->getAlert()->getValueMax() == 0 || $obValue->getValue() > $obSensor->getAlert()->getValueMax()) {
                $obSensor->getAlert()->setAlert(true);
                $obSensor->getAlert()->setDirection(1);
                $obSensor->getAlert()->setValueMax($obValue->getValue());
            }
        }
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

}
