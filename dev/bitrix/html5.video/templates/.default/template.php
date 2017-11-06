<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$uuid = uniqid();
?>

<video controls id="video<?= $uuid ?>" <? if (!empty($arParams['PREVIEW_PATH'])): ?>poster="<?= $arParams['PREVIEW_PATH'] ?>"<? endif; ?>>
    <source src="<?= $arParams['FILE_PATH'] ?>" type="video/mp4" />
</video>
<style>
    #video<?= $uuid ?> {
        max-width: 100%;
        width: <?= $arParams['WIDTH'] ?>;
        height: <?= $arParams['HEIGHT'] ?>;
    }
</style>