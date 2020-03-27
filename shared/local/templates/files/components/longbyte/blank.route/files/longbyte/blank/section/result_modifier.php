<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arResult = \Api\Core\Main\Cache::getInstance()
    ->setIblockTag(\Api\Portfolio\Element\Model::getIblockId())
    ->setId('FilesSection_' . $arParams['SECTION_CODE'])
    ->setTime(30 * 24 * 60 * 60)
    ->get(function() use ($arParams) {

    $arResult = array();

    $obSection = \Api\Files\Section\Model::getOne(array(
            'ACTIVE' => 'Y',
            '=CODE' => $arParams['SECTION_CODE']
    ));

    $obElements = \Api\Files\Element\Model::getAll(array(
            'ACTIVE' => 'Y',
            'IBLOCK_SECTION_ID' => !is_null($obSection) ? $obSection->getId() : false
    ));

    $arResult['root'] = is_null($obSection);
    $arResult['elements'] = $obElements->toArray();

    return $arResult;
});
