<?php

use Longbyte\Builder\IblockBuilder;
use WS\ReduceMigrations\Builder\Entity\Iblock;

/**
 * Class definition update migrations scenario actions
 * */
class ws_m_1553031160_kontent extends \WS\ReduceMigrations\Scenario\ScriptScenario
{

    /**
     * Name of scenario
     * */
    static public function name()
    {
        return "Контент";
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
        return "0cb6f6a0288b6db25006b285485945b8b6ff49cc";
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

        if ($arIBlock = $obBuilder->GetIblock('main', 'content')) {
            $rsIblockCatalog = $obBuilder->updateIblock($arIBlock['ID'], function (Iblock $rsIblock) {
                $this->iblockContent($rsIblock);
            });
        } else {
            $rsIblockCatalog = $obBuilder->createIblock('main', 'Контент', function (Iblock $rsIblock) {
                $this->iblockContent($rsIblock);
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

    private function iblockContent($rsIblock)
    {

        Bitrix\Main\Loader::includeModule('iblock');

        $arSiteCodes = array();
        $arSites = array();
        $rsSites = Bitrix\Main\SiteTable::getList();
        while ($arSite = $rsSites->fetch()) {
            $arSiteCodes[] = $arSite['LID'];
            $arSites[] = $arSite;
        }

        $rsIblock
            ->siteId($arSiteCodes)
            ->sort(10)
            ->code('content')
            ->xmlId('content')
            ->groupId(['2' => 'R'])
            ->setAttribute("LIST_MODE", "C")
            ->setAttribute("LIST_PAGE_URL", "/")
            ->setAttribute("SECTION_PAGE_URL", "/#SECTION_CODE_PATH#/")
            ->setAttribute("DETAIL_PAGE_URL", "/#SECTION_CODE_PATH#/#CODE#/")
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

        $IBLOCK_ID = $rsIblock->getId();
        $builder = new IblockBuilder();
        $arProps = $builder->GetPropertiesByIblockId($IBLOCK_ID);

        if (!isset($arProps['SITE'])) {
            $property = $rsIblock
                ->addProperty('Сайт')
                ->code('SITE')
                ->typeDropdown()
                ->multiple(true)
                ->sort(100)
            ;
            foreach ($arSites as $arSite) {
                $property->addEnum($arSite['NAME'])->xmlId($arSite['LID']);
            }
        } else {
            $property = $rsIblock
                ->updateProperty('Сайт')
                ->code('SITE')
                ->typeDropdown()
                ->multiple(true)
                ->sort(100)
            ;
        }

        if (!isset($arProps['MENU'])) {
            $property = $rsIblock
                ->addProperty('Меню')
                ->code('MENU')
                ->typeDropdown()
                ->multiple(true)
                ->sort(200)
            ;
            $property->addEnum('Верхнее')->xmlId('header');
            $property->addEnum('Нижнее')->xmlId('footer');
        } else {
            $property = $rsIblock
                ->updateProperty('Меню')
                ->code('MENU')
                ->typeDropdown()
                ->multiple(true)
                ->sort(200)
            ;
        }

        if (!isset($arProps['PAGE_TYPE'])) {
            $rsIblock
                ->addProperty('Тип страницы')
                ->code('PAGE_TYPE')
                ->type("S", "S:PageType")
                ->multiple(true)
                ->multipleCnt(2)
                ->sort(300)
            ;
        } else {
            $rsIblock
                ->updateProperty('Тип страницы')
                ->code('PAGE_TYPE')
                ->type("S", "S:PageType")
                ->multiple(true)
                ->multipleCnt(2)
                ->sort(300)
            ;
        }

        if (!isset($arProps['PAGE_PROPS'])) {
            $rsIblock
                ->addProperty('Свойства страницы')
                ->code('PAGE_PROPS')
                ->typeString()
                ->withDescription(true)
                ->multiple(true)
                ->sort(400)
            ;
        } else {
            $rsIblock
                ->updateProperty('Свойства страницы')
                ->code('PAGE_PROPS')
                ->typeString()
                ->withDescription(true)
                ->multiple(true)
                ->sort(400)
            ;
        }
    }

}
