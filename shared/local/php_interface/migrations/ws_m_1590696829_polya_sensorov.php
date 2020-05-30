<?php

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1590696829_polya_sensorov extends \WS\ReduceMigrations\Scenario\ScriptScenario {

    /**
     * Name of scenario
     * */
    static public function name() {
        return "Поля сенсоров";
    }

    /**
     * Priority of scenario
     * */
    static public function priority() {
        return self::PRIORITY_HIGH;
    }

    /**
     * @return string hash
     */
    static public function hash() {
        return "d8c3aa49b8d00fdd6e78353818cbcdac899559db";
    }

    /**
     * @return int approximately time in seconds
     */
    static public function approximatelyTime() {
        return 0;
    }

    /**
     * Write action by apply scenario. Use method `setData` for save need rollback data
     * */
    public function commit() {
        if (\Bitrix\Main\Loader::includeModule('iblock') && \Bitrix\Main\Loader::includeModule('highloadblock')) {

            $obHlBuilder = new \Migration\Builder\HLBuilder();

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSensors')) {
                self::hlSensor($arExistHlIblock['ID']);
            }
        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data
     * */
    public function rollback() {
        // my code
    }

    private static function hlSensor($iHlBlockId) {

        $obVisualMin = new \Migration\Builder\UserField('UF_VISUAL_MIN', 'HLBLOCK_' . $iHlBlockId);
        $obVisualMin->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obVisualMin->label(array('ru' => 'Минимум на графике', 'en' => 'Минимум на графике'));
        $obVisualMin->save();

        $obVisualMax = new \Migration\Builder\UserField('UF_VISUAL_MAX', 'HLBLOCK_' . $iHlBlockId);
        $obVisualMax->type(\Migration\Builder\UserField::TYPE_DOUBLE);
        $obVisualMax->label(array('ru' => 'Максимум на графике', 'en' => 'Муксимум на графике'));
        $obVisualMax->save();

        $obOffAlert = new \Migration\Builder\UserField('UF_OFF_ALERT', 'HLBLOCK_' . $iHlBlockId);
        $obOffAlert->type(\Migration\Builder\UserField::TYPE_DATETIME);
        $obOffAlert->label(array('ru' => 'Отключить оповещение до', 'en' => 'Отключить оповещение до'));
        $obOffAlert->save();
    }

}
