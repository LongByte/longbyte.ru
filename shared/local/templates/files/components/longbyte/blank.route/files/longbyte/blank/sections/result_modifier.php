<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arResult = \Api\Core\Main\Cache::getInstance()
    ->setIblockTag(\Api\Portfolio\Element\Model::getIblockId())
    ->setId('FilesSections')
    ->setTime(30 * 24 * 60 * 60)
    ->get(function() use ($arParams) {

    $arResult = array();

    $obSections = \Api\Files\Section\Model::getAll(array(
            'ACTIVE' => 'Y'
    ));

    $arResult['sections'] = $obSections->toArray();

    return $arResult;
});
