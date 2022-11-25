<?php

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1594409303_sensors_sortirovka extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Name of scenario
     * */
    static public function name()
    {
        return "Sensors. Сортировка.";
    }

    /**
     * Priority of scenario
     * */
    static public function priority()
    {
        return self::PRIORITY_HIGH;
    }

    /**
     * @return string hash
     */
    static public function hash()
    {
        return "cd1c7396e828c56e7d61ac44dc2f6fd33923001f";
    }

    /**
     * @return int approximately time in seconds
     */
    static public function approximatelyTime()
    {
        return 0;
    }

    /**
     * Write action by apply scenario. Use method `setData` for save need rollback data
     * */
    public function commit()
    {
        if (\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('highloadblock')) {

            $obHlBuilder = new \Migration\Builder\HLBuilder();

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSensors')) {
                self::hlSensors($arExistHlIblock['ID']);
            }
        }

//        \Bitrix\Main\Application::getConnection()->query('UPDATE sensors_sensors SET UF_LOG_MODE = 0 WHERE UF_LOG_MODE IS NULL;');

        /** @var \Api\Sensors\System\Collection $obSystems */
        /** @var \Api\Sensors\System\Entity $obSystem */
        /** @var \Api\Sensors\Sensor\Collection $obSensors */
        /** @var \Api\Sensors\Sensor\Entity $obSensor */
        $obSystems = \Api\Sensors\System\Model::getAll();

        foreach ($obSystems as $obSystem) {

            $iSort = 0;

            $obSensors = \Api\Sensors\Sensor\Model::getAll(array(
                'SYSTEM_ID' => $obSystem->getId(),
            ));
            foreach ($obSensors as $obSensor) {
                $iSort += 10;
                $obSensor->setSort($iSort);

                if (is_null($obSensor->getLogMode())) {
                    $obSensor->setLogMode(0);
                }
                if (is_null($obSensor->getAlertEnable())) {
                    $obSensor->setAlertEnable(false);
                }

                $obSensor->save();
            }
        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data
     * */
    public function rollback()
    {
        // my code
    }

    private static function hlSensors($iHlBlockId)
    {
        $obProp = new \Migration\Builder\UserField('UF_SORT', 'HLBLOCK_' . $iHlBlockId);
        $obProp->type(\Migration\Builder\UserField::TYPE_INTEGER);
        $obProp->label(array('ru' => 'Сортировка', 'en' => 'Сортировка'));
        $obProp->save();
    }

}
