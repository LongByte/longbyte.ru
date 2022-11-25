<?php

use Longbyte\Builder\IblockBuilder;
use WS\ReduceMigrations\Builder\Entity\Iblock;

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1577541767_opisanie_poley_chartov extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Name of scenario
     * */
    static public function name()
    {
        return "Описание полей чартов";
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
        return "87593f5e7002180cd1ad735283d976fc89474719";
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
        \Bitrix\Main\Loader::includeModule('iblock');

        $obProperty = new \CIBlockProperty();

        if (IntVal(IBLOCK_CHART_SYSTEMS) > 0) {

            $arProps = array();
            $rsProps = Bitrix\Iblock\PropertyTable::getList(array(
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_SYSTEMS, 'ACTIVE' => 'Y'),
                'select' => array('ID', 'CODE'),
            ));

            while ($arProp = $rsProps->fetch()) {
                $arProps[$arProp['CODE']] = $arProp;
            }

            $obProperty->update($arProps['CPU_FREQ']['ID'], array('HINT' => 'МГц'));
            $obProperty->update($arProps['CPU_BFREQ']['ID'], array('HINT' => 'МГц'));
            $obProperty->update($arProps['RAM_FREQ']['ID'], array('HINT' => 'МГц'));
            $obProperty->update($arProps['RAM_BFREQ']['ID'], array('HINT' => 'МГц'));
            $obProperty->update($arProps['GPU_CORE_FREQ']['ID'], array('HINT' => 'МГц'));
            $obProperty->update($arProps['GPU_CORE_BFREQ']['ID'], array('HINT' => 'МГц'));
            $obProperty->update($arProps['GPU_VRAM_FREQ']['ID'], array('HINT' => 'МГц'));
            $obProperty->update($arProps['GPU_VRAM_BFREQ']['ID'], array('HINT' => 'МГц'));


            $obProperty->update($arProps['CPU_CONFIG']['ID'], array('HINT' => '(nC/mT, где n - количество ялер, m - количество потоков)'));

            $obProperty->update($arProps['CPU_VCORE']['ID'], array('HINT' => 'В'));
            $obProperty->update($arProps['GPU_VCORE']['ID'], array('HINT' => 'В'));

            $obProperty->update($arProps['RAM_TIMINGS']['ID'], array('HINT' => '(10-10-10-30/2T)'));

            $obProperty->update($arProps['GPU_PCIE']['ID'], array('HINT' => '(3.0 16x)'));

            $obProperty->update($arProps['RAM']['ID'], array('HINT' => '(16GB-DDR3)'));

        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data
     * */
    public function rollback()
    {
        // my code
    }

}
