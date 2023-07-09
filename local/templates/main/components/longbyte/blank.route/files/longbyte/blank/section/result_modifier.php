<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arCache = \Api\Core\Main\Cache::getInstance()
    ->setIblockTag(\Api\Files\Element\Model::getIblockId())
    ->setId('FilesSection_' . $arParams['SECTION_CODE'])
    ->setTime(30 * 24 * 60 * 60)
    ->get(function () use ($arParams) {

        $arCache = array();

        $obIblock = new \Api\Core\Iblock\Iblock\Entity(\Api\Files\Element\Model::getIblockId());
        $obIblock->getMeta();

        /** @var \Api\Files\Section\Entity $obSection */
        $obSection = \Api\Files\Section\Model::getOneSection(array(
            'ACTIVE' => 'Y',
            '=CODE' => $arParams['SECTION_CODE'],
        ));

        if (!is_null($obSection)) {
            $obSection->getMeta();
        }

        $obElements = \Api\Files\Element\Model::getAllElements(array(
            'ACTIVE' => 'Y',
            'IBLOCK_SECTION_ID' => !is_null($obSection) ? $obSection->getId() : false,
        ));

        $arCache['obIblock'] = $obIblock;
        $arCache['obSection'] = $obSection;
        $arCache['obElements'] = $obElements;
        $arCache['root'] = !$obSection->isExists();

        return $arCache;
    })
;

$arCache['obIblock']->setMeta();
if (!is_null($arCache['obSection'])) {
    $arCache['obSection']->setMeta();
    $arCache['obSection']->addToBreadcrumbs();
}

$arResult['elements'] = $arCache['obElements']->toArray();
$arResult['root'] = $arCache['root'];
