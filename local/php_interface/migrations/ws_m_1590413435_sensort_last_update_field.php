<?php

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1590413435_sensort_last_update_field extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Name of scenario
     * */
    static public function name()
    {
        return "Sensort. Last update field";
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
        return "b54ab9ea85c87edcaf8b2c0ae7298c2287778710";
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

            if ($arExistHlIblock = $obHlBuilder->GetIblock('SensorsSystem')) {
                self::hlSystem($arExistHlIblock['ID']);
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

    private static function hlSystem($iHlBlockId)
    {

        $obLastUpdateProp = new \Migration\Builder\UserField('UF_LAST_UPDATE', 'HLBLOCK_' . $iHlBlockId);
        $obLastUpdateProp->type(\Migration\Builder\UserField::TYPE_DATETIME);
        $obLastUpdateProp->label(array('ru' => 'Последнее обновление данных', 'en' => 'Последнее обновление данных'));
        $obLastUpdateProp->save();
    }

}
