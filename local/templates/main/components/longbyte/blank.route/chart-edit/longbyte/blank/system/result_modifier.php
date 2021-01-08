<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\SectionTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\PropertyEnumerationTable;
use AB\Iblock\Element;

Loader::includeModule('iblock');
Loader::includeModule('ab.iblock');

$obRequest = \Bitrix\Main\Context::getCurrent()->getRequest();

$arResult['SYSTEM'] = array();

$arSystem = ElementTable::getRow(array(
        'select' => array('ID', 'IBLOCK_ID', 'NAME'),
        'filter' => array(
            'IBLOCK_ID' => IBLOCK_CHART_SYSTEMS,
            '=ACTIVE' => 'Y',
            '=XML_ID' => $arParams['SYSTEM_XML_ID']
        ),
    ));

if (!$arSystem) {
    \Bitrix\Iblock\Component\Tools::process404('Ничего нет', true, true, true, '');
    return 0;
} else {

    if ($obRequest->isPost()) {
        $obElement = new \CIBlockElement();

        $arSystemFields = $obRequest->get('system');
        if ($arSystemFields['ID'] != $arSystem['ID']) {
            \Bitrix\Iblock\Component\Tools::process404('Ничего нет', true, true, true, '');
            return 0;
        }
        $arSystemProps = $arSystemFields['PROPERTY_VALUES'];
        unset($arSystemFields['ID']);
        unset($arSystemFields['PROPERTY_VALUES']);
        $obElement->Update($arSystem['ID'], $arSystemFields);
        \CIBlockElement::SetPropertyValuesEx($arSystem['ID'], $arSystem['IBLOCK_ID'], $arSystemProps);

        $arTests = array();
        $rsTest = Element::getList(array(
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_TESTS),
                'select' => array('ID', 'NAME'),
        ));

        while ($arTest = $rsTest->fetch()) {
            $arTests[$arTest['ID']] = $arTest;
        }

        $arResultsSections = array();
        $rsResultsSections = SectionTable::getList(array(
                'select' => array('ID', 'NAME'),
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_RESULT),
        ));

        while ($arResultsSection = $rsResultsSections->fetch()) {
            $arResultsSections[$arResultsSection['NAME']] = $arResultsSection;
        }

        $arResults = array();
        $rsResults = Element::getList(array(
                'filter' => array('IBLOCK_ID' => IBLOCK_CHART_RESULT, '=ACTIVE' => 'Y', 'PROPERTY.SYSTEM.ID' => $arSystem['ID']),
                'select' => array(
                    'ID',
                    'TEST_ID' => 'PROPERTY.TEST_ID.ID',
                ),
        ));

        while ($arOneResult = $rsResults->fetch()) {
            $arResults[$arOneResult['TEST_ID']] = $arOneResult;
        }

        $arResultsData = $obRequest->get('result');

        foreach ($arResultsData as $iTestId => $arTestResult) {
            if (isset($arResults[$iTestId])) {
                $obElement->Update($arResults[$iTestId]['ID'], array('PREVIEW_TEXT' => $arTestResult['info']));
                \CIBlockElement::SetPropertyValuesEx($arResults[$iTestId]['ID'], IBLOCK_CHART_RESULT, array(
                    'RESULT' => $arTestResult['result'],
                    'RESULT2' => $arTestResult['result2'],
                    'RESULT3' => $arTestResult['result3']
                ));
            } else {
                $arResultFields = array(
                    'NAME' => $arSystem['NAME'],
                    'IBLOCK_ID' => IBLOCK_CHART_RESULT,
                    'PREVIEW_TEXT' => $arTestResult['info'],
                    'ACTIVE' => 'Y',
                    'IBLOCK_SECTION_ID' => $arResultsSections[$arTests[$iTestId]['NAME']]['ID'],
                    'PROPERTY_VALUES' => array(
                        'RESULT' => $arTestResult['result'],
                        'RESULT2' => $arTestResult['result2'],
                        'RESULT3' => $arTestResult['result3'],
                        'TEST_ID' => $iTestId,
                        'SYSTEM_ID' => $arSystem['ID'],
                    )
                );
                $obElement->Add($arResultFields);
            }
        }
    }
    unset($arTests);

    $obSystem = \CIBlockElement::GetList(
            array(),
            array(
                'IBLOCK_ID' => IBLOCK_CHART_SYSTEMS,
                'ACTIVE' => 'Y',
                '=XML_ID' => $arParams['SYSTEM_XML_ID']
            ),
            false,
            array('nTopCount' => 1),
            array('ID', 'NAME', 'CODE', 'IBLOCK_ID', 'SORT', 'IBLOCK_SECTION_ID')
        )->GetNextElement();

    $arSystem = $obSystem->GetFields();
    $arSystem['PROPERTIES'] = $obSystem->GetProperties(false, false);

    $arSections = array();
    $rsSections = SectionTable::getList(array(
            'filter' => array('IBLOCK_ID' => IBLOCK_CHART_SYSTEMS, '=ACTIVE' => 'Y'),
            'select' => array('ID', 'NAME'),
            'order' => array('SORT' => 'ASC', 'ID' => 'ASC')
    ));

    while ($arSection = $rsSections->fetch()) {
        $arSections[] = array(
            'ID' => $arSection['ID'],
            'VALUE' => $arSection['NAME'],
        );
    }

    $arResult['SYSTEM'] = array(
        array(
            'CODE' => 'system[ID]',
            'NAME' => 'ID',
            'VALUE' => $arSystem['ID'],
            'TYPE' => 'hidden',
            'MULTIPLE' => false,
            'VALUES' => array(),
        ),
        array(
            'CODE' => 'system[NAME]',
            'NAME' => 'Название',
            'VALUE' => $arSystem['NAME'],
            'TYPE' => 'text',
            'MULTIPLE' => false,
            'VALUES' => array(),
        ),
        array(
            'CODE' => 'system[IBLOCK_SECTION_ID]',
            'NAME' => 'Тип',
            'VALUE' => $arSystem['IBLOCK_SECTION_ID'],
            'TYPE' => 'select',
            'MULTIPLE' => false,
            'VALUES' => $arSections,
        ),
    );

    $arSectionLink_Cache = array();
    $arElementLink_Cache = array();

    $arEnums = array();
    $rsEnums = PropertyEnumerationTable::getList(array(
            'select' => array(
                'ID',
                'PROPERTY_ID',
                'VALUE',
                'XML_ID',
            ),
            'filter' => array(
                'PROPERTY.IBLOCK_ID' => IBLOCK_CHART_SYSTEMS
            ),
            'order' => array(
                'SORT' => 'ASC',
                'VALUE' => 'ASC',
            ),
    ));

    while ($arEnum = $rsEnums->fetch()) {
        $arEnums[$arEnum['PROPERTY_ID']][] = $arEnum;
    }

    foreach ($arSystem['PROPERTIES'] as $arProp) {

        $arValues = array();
        $type = 'text';
        if ($arProp['USER_TYPE'] == 'SASDCheckboxNum') {
            $type = 'checkbox';
        }
        if (in_array($arProp['PROPERTY_TYPE'], array(PropertyTable::TYPE_ELEMENT, PropertyTable::TYPE_SECTION, PropertyTable::TYPE_LIST))) {
            $type = 'select';
        }

        if ($arProp['PROPERTY_TYPE'] == PropertyTable::TYPE_ELEMENT) {
            if (!isset($arElementLink_Cache[$arProp['IBLOCK_LINK_ID']])) {
                $rsElements = ElementTable::getList(array(
                        'filter' => array('IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'], '=ACTIVE' => 'Y'),
                        'select' => array('ID', 'NAME'),
                        'order' => array('SORT' => 'ASC', 'ID' => 'ASC')
                ));

                while ($arElement = $rsElements->fetch()) {
                    $arElementLink_Cache[$arProp['IBLOCK_LINK_ID']][] = array(
                        'ID' => $arElement['ID'],
                        'VALUE' => $arElement['NAME'],
                    );
                }
            }
            $arValues = $arElementLink_Cache[$arProp['IBLOCK_LINK_ID']];
        }

        if ($arProp['PROPERTY_TYPE'] == PropertyTable::TYPE_SECTION) {
            if (!isset($arSectionLink_Cache[$arProp['IBLOCK_LINK_ID']])) {
                $rsSections = SectionTable::getList(array(
                        'filter' => array('IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'], '=ACTIVE' => 'Y'),
                        'select' => array('ID', 'NAME'),
                        'order' => array('SORT' => 'ASC', 'ID' => 'ASC')
                ));

                while ($arSection = $rsSections->fetch()) {
                    $arSectionLink_Cache[$arProp['IBLOCK_LINK_ID']][] = array(
                        'ID' => $arSection['ID'],
                        'VALUE' => $arSection['NAME'],
                    );
                }
            }
            $arValues = $arSectionLink_Cache[$arProp['IBLOCK_LINK_ID']];
        }

        if ($arProp['PROPERTY_TYPE'] == PropertyTable::TYPE_LIST) {
            $arValues = $arEnums[$arProp['ID']];
        }
        
        if ($arProp['MULTIPLE'] == 'Y' && !$arProp['VALUE']) {
            $arProp['VALUE'] = array();
        }

        $arResult['SYSTEM'][] = array(
            'CODE' => 'system[PROPERTY_VALUES][' . $arProp['CODE'] . ']' . ($arProp['MULTIPLE'] == 'Y' ? '[]' : ''),
            'NAME' => $arProp['NAME'],
            'HINT' => $arProp['HINT'],
            'VALUE' => $arProp['VALUE'],
            'TYPE' => $type,
            'MULTIPLE' => $arProp['MULTIPLE'] == 'Y',
            'VALUES' => $arValues,
        );
    }

    $arSystems = array();

    $arSelect = array(
        'ID',
        'NAME',
    );

    $arSystemProps = array();
    $rsProps = PropertyTable::getList(array(
            'filter' => array('IBLOCK_ID' => IBLOCK_CHART_SYSTEMS, 'ACTIVE' => 'Y'),
            'select' => array('ID', 'CODE', 'PROPERTY_TYPE', 'MULTIPLE')
    ));

    while ($arProp = $rsProps->fetch()) {
        $arSystemProps['PROP_' . $arProp['CODE']] = $arProp;
        if ($arProp['PROPERTY_TYPE'] == 'E') {
            $arSelect['PROP_' . $arProp['CODE'] . '_VALUE'] = 'PROPERTY.' . $arProp['CODE'] . '.NAME';
            $arSelect['PROP_' . $arProp['CODE'] . '_TEXT_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.XML_ID';
            $arSelect['PROP_' . $arProp['CODE'] . '_PASSIVE_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.PREVIEW_TEXT';
            $arSelect['PROP_' . $arProp['CODE'] . '_ACTIVE_COLOR'] = 'PROPERTY.' . $arProp['CODE'] . '.DETAIL_TEXT';
        } else {
            $arSelect['PROP_' . $arProp['CODE']] = 'PROPERTY.' . $arProp['CODE'];
        }
    }

    $rsSytems = Element::getList(array(
            'filter' => array('IBLOCK_ID' => IBLOCK_CHART_SYSTEMS, 'ACTIVE' => 'Y', 'ID' => $arSystem['ID']),
            'select' => $arSelect,
    ));

    while ($arOneSystem = $rsSytems->fetch()) {
        if (!isset($arSystems[$arOneSystem['ID']])) {
            foreach ($arOneSystem as $field => &$value) {
                if ($arSystemProps[$field]['MULTIPLE'] == 'Y') {
                    if (is_null($value)) {
                        $value = array();
                    } else {
                        $value = array($value);
                    }
                }
            }
            unset($value);
            $arSystems[$arOneSystem['ID']] = $arOneSystem;
        } else {
            foreach ($arOneSystem as $field => &$value) {
                if ($arSystemProps[$field]['MULTIPLE'] == 'Y' && !is_null($value) && !in_array($value, $arSystems[$arOneSystem['ID']][$field])) {
                    $arSystems[$arOneSystem['ID']][$field][] = $value;
                }
            }
            unset($value);
        }
    }

    $arResult['TEST_TYPES'] = array();
    $rsTestTypes = SectionTable::getList(array(
            'order' => array('SORT' => 'ASC'),
            'filter' => array('IBLOCK_ID' => IBLOCK_CHART_TESTS, '=ACTIVE' => 'Y'),
            'select' => array('ID', 'NAME', 'TYPE' => 'CODE', 'DESCRIPTION')
    ));

    while ($arTestType = $rsTestTypes->fetch()) {
        $arResult['TEST_TYPES'][(string) $arTestType['ID']] = $arTestType;
    }

    $rsTest = Element::getList(array(
            'order' => array('SORT' => 'ASC'),
            'filter' => array('IBLOCK_ID' => IBLOCK_CHART_TESTS, '=ACTIVE' => 'Y'),
            'select' => array(
                'ID',
                'NAME',
                'DESCRIPTION' => 'PREVIEW_TEXT',
                'TEST_TYPE' => 'IBLOCK_SECTION_ID',
                'UNITS' =>
                'PROPERTY.UNITS',
                'PLACEHOLDER_RESULT' => 'PROPERTY.PLACEHOLDER_RESULT',
                'PLACEHOLDER_RESULT2' => 'PROPERTY.PLACEHOLDER_RESULT2',
                'PLACEHOLDER_RESULT3' => 'PROPERTY.PLACEHOLDER_RESULT3',
                'PLACEHOLDER_INFO' => 'DETAIL_TEXT',
            ),
    ));

    while ($arTest = $rsTest->fetch()) {
        $arResult['TEST_TYPES'][$arTest['TEST_TYPE']]['TESTS'][(string) $arTest['ID']] = $arTest;
    }

    $rsResults = Element::getList(array(
            'filter' => array('IBLOCK_ID' => IBLOCK_CHART_RESULT, 'ACTIVE' => 'Y', 'PROPERTY.SYSTEM_ID.ID' => $arSystem['ID']),
            'select' => array(
                'ID',
                'NAME',
                'INFO' => 'PREVIEW_TEXT',
                'TEST_ID' => 'PROPERTY.TEST_ID.ID',
                'TEST_TYPE' => 'PROPERTY.TEST_ID.IBLOCK_SECTION_ID',
                'RESULT' => 'PROPERTY.RESULT',
                'RESULT2' => 'PROPERTY.RESULT2',
                'RESULT3' => 'PROPERTY.RESULT3',
                'SYSTEM_ID' => 'PROPERTY.SYSTEM_ID.ID',
            ),
    ));


    while ($arRes = $rsResults->fetch()) {
        if (isset($arResult['TEST_TYPES'][$arRes['TEST_TYPE']])) {
            if (isset($arResult['TEST_TYPES'][$arRes['TEST_TYPE']]['TESTS'][$arRes['TEST_ID']])) {
                if (isset($arSystems[$arRes['SYSTEM_ID']])) {
                    $arResult['TEST_TYPES'][$arRes['TEST_TYPE']]['TESTS'][$arRes['TEST_ID']]['RESULT'] = $arRes;
                }
            }
        }
    }

    $arResult['VUE'] = $arResult;
    
    $arResult['FORMAT'] = $obRequest->isPost() ? 'json' : 'html';
}