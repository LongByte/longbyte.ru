<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Loader;
use Bitrix\Iblock\SectionTable;
//use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\PropertyTable;
use AB\Iblock\Element;

if ($this->startResultCache(60 * 60)) {

    $iblockSystem = 1;
    $iblockFirm = 2;
    $iblockTest = 3;
    $iblockResult = 4;

    Loader::includeModule('iblock');
    Loader::includeModule('ab.iblock');

    $arResult = array('TEST_TYPES' => array());
    $rsTestTypes = SectionTable::getList(array(
            'order' => array('SORT' => 'ASC'),
            'filter' => array('IBLOCK_ID' => $iblockTest, 'ACTIVE' => 'Y'),
            'select' => array('ID', 'NAME', 'TYPE' => 'CODE', 'DESCRIPTION')
    ));

    while ($arTestType = $rsTestTypes->fetch()) {
        $arResult['TEST_TYPES'][(string) $arTestType['ID']] = $arTestType;
    }

    $rsTest = Element::getList(array(
            'order' => array('SORT' => 'ASC'),
            'filter' => array('IBLOCK_ID' => $iblockTest, 'ACTIVE' => 'Y'),
            'select' => array(
                'ID',
                'NAME',
                'DESCRIPTION' => 'PREVIEW_TEXT',
                'TEST_TYPE' => 'IBLOCK_SECTION_ID',
                'UNITS' =>
                'PROPERTY.UNITS',
                'LESS_BETTER' => 'PROPERTY.LESS_BETTER',
                'USE4SUM' => 'PROPERTY.USE4SUM',
                'USE4SUM2' => 'PROPERTY.USE4SUM2',
                'USE4SUM3' => 'PROPERTY.USE4SUM3'
            ),
    ));

    while ($arTest = $rsTest->fetch()) {
        $arTest['MAX'] = 0;
        $arTest['MIN'] = PHP_INT_MAX;
        $arTest['MAX2'] = 0;
        $arTest['MIN2'] = PHP_INT_MAX;
        $arTest['MAX3'] = 0;
        $arTest['MIN3'] = PHP_INT_MAX;
        $arResult['TEST_TYPES'][$arTest['TEST_TYPE']]['TESTS'][(string) $arTest['ID']] = $arTest;
    }

    $arSystems = array();

    $arFilter = array(
        'ID',
        'NAME',
    );

    $rsProps = PropertyTable::getList(array(
            'filter' => array('IBLOCK_ID' => $iblockSystem, 'ACTIVE' => 'Y'),
            'select' => array('CODE', 'PROPERTY_TYPE')
    ));

    while ($arProp = $rsProps->fetch()) {
        if ($arProp['PROPERTY_TYPE'] == 'E') {
            $arFilter['PROP_' . $arProp['CODE'] . '_VALUE'] = 'PROPERTY.' . $arProp['CODE'] . '.NAME';
            $arFilter['PROP_' . $arProp['CODE'] . '_TEXT_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.XML_ID';
            $arFilter['PROP_' . $arProp['CODE'] . '_PASSIVE_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.PREVIEW_TEXT';
            $arFilter['PROP_' . $arProp['CODE'] . '_ACTIVE_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.DETAIL_TEXT';
        } else {
            $arFilter['PROP_' . $arProp['CODE']] = 'PROPERTY.' . $arProp['CODE'];
        }
    }

    $rsSytems = Element::getList(array(
            'filter' => array('IBLOCK_ID' => $iblockSystem, 'ACTIVE' => 'Y'),
            'select' => $arFilter,
    ));

    while ($arSystem = $rsSytems->fetch()) {
        $arSystems[$arSystem['ID']] = $arSystem;
    }

    $rsResults = Element::getList(array(
            'filter' => array('IBLOCK_ID' => $iblockResult, 'ACTIVE' => 'Y'),
            'select' => array(
                'ID',
                'NAME',
                'INFO' => 'PREVIEW_TEXT',
                'TEST' => 'PROPERTY.TEST.ID',
                'TEST_TYPE' => 'PROPERTY.TEST.IBLOCK_SECTION_ID',
                'RESULT' => 'PROPERTY.RESULT',
                'RESULT2' => 'PROPERTY.RESULT2',
                'RESULT3' => 'PROPERTY.RESULT3',
                'SYSTEM' => 'PROPERTY.SYSTEM.ID',
            ),
    ));

    while ($arRes = $rsResults->fetch()) {
        if (isset($arResult['TEST_TYPES'][$arRes['TEST_TYPE']])) {
            if (isset($arResult['TEST_TYPES'][$arRes['TEST_TYPE']]['TESTS'][$arRes['TEST']])) {
                if (isset($arSystems[$arRes['SYSTEM']])) {
                    $arRes['SYSTEM'] = $arSystems[$arRes['SYSTEM']];
                    $arTest = &$arResult['TEST_TYPES'][$arRes['TEST_TYPE']]['TESTS'][$arRes['TEST']];
                    $arTest['ITEMS'][$arRes['ID']] = $arRes;
                    if ($arTest['USE4SUM']) {
                        $arTest['MAX'] = max($arTest['MAX'], $arRes['RESULT']);
                        $arTest['MIN'] = min($arTest['MIN'], $arRes['RESULT']);
                    }
                    if ($arTest['USE4SUM2']) {
                        $arTest['MAX2'] = max($arTest['MAX2'], $arRes['RESULT2']);
                        $arTest['MIN2'] = min($arTest['MIN2'], $arRes['RESULT2']);
                    }
                    if ($arTest['USE4SUM3']) {
                        $arTest['MAX3'] = max($arTest['MAX3'], $arRes['RESULT3']);
                        $arTest['MIN3'] = min($arTest['MIN3'], $arRes['RESULT3']);
                    }

                    unset($arTest);
                }
            }
        }
    }

    foreach ($arResult['TEST_TYPES'] as &$arTestType) {
        $arResultTest = array(
            'ID' => '',
            'NAME' => 'Итог',
            'DESCRIPTION' => $arTestType['DESCRIPTION'],
            'TEST_TYPE' => $arTestType['ID'],
            'UNITS' => '',
            'LESS_BETTER' => 0,
            'USE4SUM' => 0,
            'MAX' => 0,
            'TESTS' => 0,
            'ITEMS' => array()
        );
        foreach ($arTestType['TESTS'] as &$arTest) {
            $bRamTest = in_array($arTest['ID'], array(31, 32, 33, 34));

            if (count($arTest['ITEMS']) > 0) {
                if ($arTest['USE4SUM']) {
                    $arResultTest['MAX'] += 100;
                    $arResultTest['TESTS'] ++;
                }
                if ($arTest['USE4SUM2']) {
                    $arResultTest['MAX2'] += 100;
                    $arResultTest['TESTS'] ++;
                }
                if ($arTest['USE4SUM3']) {
                    $arResultTest['MAX3'] += 100;
                    $arResultTest['TESTS'] ++;
                }
            }
            foreach ($arTest['ITEMS'] as &$arItem) {
                if (!isset($arResultTest['ITEMS'][$arItem['SYSTEM']['ID']])) {
                    $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']] = array(
                        'ID' => $arItem['SYSTEM']['ID'],
                        'NAME' => $arItem['SYSTEM']['NAME'],
                        'SYSTEM' => $arItem['SYSTEM'],
                        'TEST_TYPE' => $arTestType['ID'],
                        'RESULT' => 0,
                        'INFO' => '',
                        'TEST' => $arResultTest['ID'],
                        'TESTS' => 0
                    );
                }

                if ($arTest['USE4SUM']) {
                    if (!empty($arItem['RESULT'])) {
                        $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['TESTS'] ++;

                        if ($bRamTest) {
                            if (!$arTest['LESS_BETTER']) {
                                $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['RESULT'] += round($arItem['RESULT'] / $arItem['RESULT2'] * 100 / 4, 2);
                            } else {
                                $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['RESULT'] += round((100 - $arItem['RESULT']) * 2 / 4, 2);
                            }
                        } elseif (!$arTest['LESS_BETTER']) {
                            $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['RESULT'] += floor($arItem['RESULT'] / $arTest['MAX'] * 100);
                        } else {
                            $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['RESULT'] += floor(($arTest['MAX'] - $arItem['RESULT']) / ($arTest['MAX'] - $arTest['MIN']) * 100);
                        }
                    }
                }
                if ($arTest['USE4SUM2']) {
                    if (!empty($arItem['RESULT2'])) {
                        $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['TESTS'] ++;

                        if (!$arTest['LESS_BETTER']) {
                            $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['RESULT'] += floor($arItem['RESULT2'] / $arTest['MAX2'] * 100);
                        } else {
                            $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['RESULT'] += floor(($arTest['MAX2'] - $arItem['RESULT2']) / ($arTest['MAX2'] - $arTest['MIN2']) * 100);
                        }
                    }
                }
                if ($arTest['USE4SUM3']) {
                    if (!empty($arItem['RESULT3'])) {
                        $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['TESTS'] ++;

                        if (!$arTest['LESS_BETTER']) {
                            $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['RESULT'] += floor($arItem['RESULT3'] / $arTest['MAX3'] * 100);
                        } else {
                            $arResultTest['ITEMS'][$arItem['SYSTEM']['ID']]['RESULT'] += floor(($arTest['MAX3'] - $arItem['RESULT3']) / ($arTest['MAX3'] - $arTest['MIN3']) * 100);
                        }
                    }
                }
            }
            unset($arItem);
        }
        unset($arTest);
        foreach ($arResultTest['ITEMS'] as $i => &$arItem) {
            if ($arItem['TESTS'] < $arResultTest['TESTS']) {
                unset($arResultTest['ITEMS'][$i]);
            }
        }
        unset($arItem);
        $arTestType['TESTS'][] = $arResultTest;
        unset($arResultTest);
    }
    unset($arTestType);

//echo "<pre>";
//print_r($arResult);
//echo "</pre>";

    $this->IncludeComponentTemplate();
}
?>