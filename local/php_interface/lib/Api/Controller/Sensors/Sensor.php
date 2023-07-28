<?

namespace Api\Controller\Sensors;

/**
 * Class \Api\Controller\Sensors\Sensor
 */
class Sensor extends \Api\Core\Base\Controller
{
    private ?string $token = null;

    private ?\Api\Sensors\System\Entity $obSystem = null;

    public function __construct()
    {
        parent::__construct();
        $this->token = $this->getRequest()->get('token');
    }

    public function get()
    {
        if (!$this->loadSystem()) {
            return;
        }

        $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
            'SYSTEM_ID' => $this->getSystem()->getId(),
        ), 0, 0, array(
            'order' => array('SORT' => 'ASC'),
        ));

        $this->getResponse()->setData($obSensors->toSensor());
    }

    private function loadSystem(): bool
    {

        $this->obSystem = \Api\Sensors\System\Model::getOne(array(
            '=TOKEN' => $this->token,
            'ACTIVE' => true,
        ));

        if ($this->obSystem) {
            return true;
        } else {
            $this->getResponse()->addError('Неверный токен');
        }

        return false;
    }

    private function getSystem(): ?\Api\Sensors\System\Entity
    {
        return $this->obSystem;
    }
}
