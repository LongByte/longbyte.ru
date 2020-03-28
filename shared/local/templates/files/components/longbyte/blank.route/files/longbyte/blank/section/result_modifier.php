<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arCache = \Api\Core\Main\Cache::getInstance()
    ->setIblockTag(\Api\Files\Element\Model::getIblockId())
    ->setId('FilesSection_' . $arParams['SECTION_CODE'])
    ->setTime(30 * 24 * 60 * 60)
    ->get(function() use ($arParams) {

    $arCache = array();

    $obIblock = new \Api\Core\Iblock\Iblock\Entity(\Api\Files\Element\Model::getIblockId());
    $obIblock->getMeta();

    $obSection = \Api\Files\Section\Model::getOne(array(
            'ACTIVE' => 'Y',
            '=CODE' => $arParams['SECTION_CODE']
    ));
    
    $obSection->getMeta();

    $obElements = \Api\Files\Element\Model::getAll(array(
            'ACTIVE' => 'Y',
            'IBLOCK_SECTION_ID' => !is_null($obSection) ? $obSection->getId() : false
    ));

    $arCache['iblock'] = $obIblock;
    $arCache['section'] = $obSection;
    $arCache['elements'] = $obElements;
    $arCache['root'] = is_null($obSection);

    return $arCache;
});

$arCache['iblock']->setMeta();
$arCache['section']->setMeta();
$arCache['section']->addToBreadcrumbs();

$arResult['elements'] = $arCache['elements']->toArray();
$arResult['root'] = $arCache['root'];
