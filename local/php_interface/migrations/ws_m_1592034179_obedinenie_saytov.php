<?php

use Migration\Builder\IblockBuilder;
use WS\ReduceMigrations\Builder\Entity\Iblock;

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1592034179_obedinenie_saytov extends \WS\ReduceMigrations\Scenario\ScriptScenario {

    /**
     * Name of scenario
     * */
    static public function name() {
        return "Объединение сайтов";
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
        return "be24376065fc90500aa610efaa840639154d409b";
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
        \Bitrix\Main\Loader::includeModule('iblock');
        $obBuilder = new IblockBuilder();

        $rsIblocks = \Bitrix\Iblock\IblockTable::getList();

        while ($arIblock = $rsIblocks->fetch()) {

            $rsIblockCatalog = $obBuilder->updateIblock($arIblock['ID'], function (Iblock $rsIblock) {
                $rsIblock->siteId('s1');
            });
        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data
     * */
    public function rollback() {
// my code
    }

}
