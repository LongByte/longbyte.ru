<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

global $USER;

if (!$USER->isAdmin()) {
    \Bitrix\Iblock\Component\Tools::process404('Ничего нет', true, true, true, '');
    return 0;
} else {

    $arResult['VUE'] = array(
        'groups' => array(),
    );

    \Bitrix\Main\Loader::includeModule('iblock');

    $arSystems = array();
    $rsSystems = Bitrix\Iblock\ElementTable::getList(array(
            'select' => array(
                'ID',
                'NAME',
                'IBLOCK_SECTION_ID',
                'XML_ID',
            ),
            'filter' => array(
                'IBLOCK_ID' => IBLOCK_CHART_SYSTEMS,
                'ACTIVE' => 'Y',
            ),
            'order' => array(
                'SORT' => 'ASC',
                'NAME' => 'ASC',
            ),
    ));

    while ($arSystem = $rsSystems->fetch()) {
        $arSystems[$arSystem['ID']] = $arSystem;
    }

    $rsGroups = \Bitrix\Iblock\SectionTable::getList(array(
            'select' => array(
                'ID',
                'NAME',
            ),
            'filter' => array(
                'IBLOCK_ID' => IBLOCK_CHART_SYSTEMS,
                'ACTIVE' => 'Y',
            ),
            'order' => array(
                'SORT' => 'ASC',
            ),
    ));


    while ($arGroup = $rsGroups->fetch()) {
        $arInnerSystems = array();
        foreach ($arSystems as $arSystem) {
            if ($arSystem['IBLOCK_SECTION_ID'] == $arGroup['ID']) {
                $arInnerSystems[] = array(
                    'name' => $arSystem['NAME'],
                    'url' => '/admin/' . $arSystem['XML_ID'] . '/',
                );
            }
        }
        $arResult['VUE']['groups'][] = array(
            'name' => $arGroup['NAME'],
            'systems' => $arInnerSystems,
        );
    }
}