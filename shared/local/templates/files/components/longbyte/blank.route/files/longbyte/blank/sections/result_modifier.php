<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arCache = \Api\Core\Main\Cache::getInstance()
    ->setIblockTag(\Api\Files\Element\Model::getIblockId())
    ->setId('FilesSections')
    ->setTime(30 * 24 * 60 * 60)
    ->get(function() use ($arParams) {

    $arCache = array();

    $obIblock = new \Api\Core\Iblock\Iblock\Entity(\Api\Files\Element\Model::getIblockId());
    $obIblock->getMeta();
    
    $obSections = \Api\Files\Section\Model::getAll(array(
            'ACTIVE' => 'Y'
    ));

    $arCache['iblock'] = $obIblock;
    $arCache['sections'] = $obSections;

    return $arCache;
});

$arCache['iblock']->setMeta();

$arResult['sections'] = $arCache['sections']->toArray();

