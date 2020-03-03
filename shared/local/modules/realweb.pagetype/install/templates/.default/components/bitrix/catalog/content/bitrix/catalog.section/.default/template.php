<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<? if (strlen($arResult['REDIRECT']) == 0): ?>
    <? if (is_array($arResult['PICTURE'])): ?>
        <img src="<?= $arResult['PICTURE']['SRC']; ?>" alt="<?= $arResult['PICTURE']['ALT']; ?>" title="<?= $arResult['PICTURE']['TITLE']; ?>" class="" />
    <? endif; ?>
    <? if (strlen($arResult['DESCRIPTION']) > 0): ?>
        <?= $arResult['DESCRIPTION']; ?>
    <? endif; ?>
<? endif; ?>





