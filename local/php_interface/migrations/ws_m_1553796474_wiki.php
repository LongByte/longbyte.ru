<?php

use Longbyte\Builder\IblockBuilder;
use WS\ReduceMigrations\Builder\Entity\Iblock;

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1553796474_wiki extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Name of scenario
     * */
    static public function name()
    {
        return "Wiki";
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
        return "93596e3b51598022f10b3611006760723b461ab6";
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
        $obBuilder = new IblockBuilder();

        if ($arIBlock = $obBuilder->GetIblock('main', 'wiki')) {
            $rsIblockCatalog = $obBuilder->updateIblock($arIBlock['ID'], function (Iblock $rsIblock) {
                $this->iblockWiki($rsIblock);
            });
        } else {
            $rsIblockCatalog = $obBuilder->createIblock('main', 'Wiki', function (Iblock $rsIblock) {
                $this->iblockWiki($rsIblock);
            });
        }
    }

    /**
     * Write action by rollback scenario. Use method `getData` for getting commit saved data
     * */
    public function rollback()
    {
        // my code
    }

    private function iblockWiki($rsIblock)
    {

        Bitrix\Main\Loader::includeModule('iblock');

        $rsIblock
            ->siteId('s1')
            ->sort(10)
            ->code('wiki')
            ->xmlId('wiki')
            ->groupId(['2' => 'R'])
            ->setAttribute("LIST_MODE", "C")
            ->setAttribute("LIST_PAGE_URL", "/")
            ->setAttribute("SECTION_PAGE_URL", "/#SECTION_CODE_PATH#/")
            ->setAttribute("DETAIL_PAGE_URL", "/#SECTION_CODE_PATH#/#ELEMENT_CODE#/")
            ->setAttribute('FIELDS', array(
                'CODE' => array(
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => array(
                        'UNIQUE' => 'Y',
                        'TRANSLITERATION' => 'Y',
                        'TRANS_LEN' => '100',
                        'TRANS_CASE' => 'L',
                        'TRANS_SPACE' => '-',
                        'TRANS_OTHER' => '-',
                        'TRANS_EAT' => 'Y',
                        'USE_GOOGLE' => 'N',
                    ),
                ),
                'SECTION_CODE' => array(
                    'IS_REQUIRED' => 'Y',
                    'DEFAULT_VALUE' => array(
                        'UNIQUE' => 'Y',
                        'TRANSLITERATION' => 'Y',
                        'TRANS_LEN' => '100',
                        'TRANS_CASE' => 'L',
                        'TRANS_SPACE' => '-',
                        'TRANS_OTHER' => '-',
                        'TRANS_EAT' => 'Y',
                        'USE_GOOGLE' => 'N',
                    ),
                ),
            ))
        ;
    }

}
