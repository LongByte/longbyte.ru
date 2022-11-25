<?php

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1601844351_chart_kody_svoystv extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Scenario title
     * */
    public static function name()
    {
        return 'chart. Коды свойств';
    }

    /**
     * Priority of scenario
     * */
    public static function priority()
    {
        return self::PRIORITY_HIGH;
    }

    /**
     * @return string hash
     */
    public static function hash()
    {
        return 'fcd5fd801d49cb68f78f0a31507ddf81d663faf0';
    }

    /**
     * @return int approximately time in seconds
     */
    public static function approximatelyTime()
    {
        return 0;
    }

    /**
     * Writes action by apply scenario. Use method `setData` to save needed rollback data.
     * For printing info into console use object from $this->printer() method.
     * */
    public function commit()
    {
        \Bitrix\Main\Loader::includeModule('iblock');

        if ($arProp = \Bitrix\Iblock\PropertyTable::getRow(array(
            'filter' => array(
                'IBLOCK_ID' => IBLOCK_CHART_RESULT,
                '=CODE' => 'TEST',
            ),
        ))) {
            \Bitrix\Iblock\PropertyTable::update($arProp['ID'], array(
                'CODE' => 'TEST_ID',
            ));
        }

        if ($arProp = \Bitrix\Iblock\PropertyTable::getRow(array(
            'filter' => array(
                'IBLOCK_ID' => IBLOCK_CHART_RESULT,
                '=CODE' => 'SYSTEM',
            ),
        ))) {
            \Bitrix\Iblock\PropertyTable::update($arProp['ID'], array(
                'CODE' => 'SYSTEM_ID',
            ));
        }

        if ($arProp = \Bitrix\Iblock\PropertyTable::getRow(array(
            'filter' => array(
                'IBLOCK_ID' => IBLOCK_CHART_SYSTEMS,
                '=CODE' => 'CPU_FIRM',
            ),
        ))) {
            \Bitrix\Iblock\PropertyTable::update($arProp['ID'], array(
                'CODE' => 'CPU_FIRM_ID',
            ));
        }

        if ($arProp = \Bitrix\Iblock\PropertyTable::getRow(array(
            'filter' => array(
                'IBLOCK_ID' => IBLOCK_CHART_SYSTEMS,
                '=CODE' => 'GPU_FIRM',
            ),
        ))) {
            \Bitrix\Iblock\PropertyTable::update($arProp['ID'], array(
                'CODE' => 'GPU_FIRM_ID',
            ));
        }

        if ($arProp = \Bitrix\Iblock\PropertyTable::getRow(array(
            'filter' => array(
                'IBLOCK_ID' => IBLOCK_CHART_SYSTEMS,
                '=CODE' => 'HD_FIRM',
            ),
        ))) {
            \Bitrix\Iblock\PropertyTable::update($arProp['ID'], array(
                'CODE' => 'HD_FIRM_ID',
            ));
        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data.
     * For printing info into console use object from $this->printer() method.
     * */
    public function rollback()
    {
// my code
    }

}
