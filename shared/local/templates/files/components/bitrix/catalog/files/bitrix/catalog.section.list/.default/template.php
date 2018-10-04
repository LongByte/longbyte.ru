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


foreach ($arResult['SECTIONS'] as &$arSection) {
    $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], $strSectionEdit);
    $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], $strSectionDelete, $arSectionDeleteParams);
    ?>
    <a class="item" href="<?= $arSection['SECTION_PAGE_URL'] ?>" id="<?= $this->GetEditAreaId($arSection['ID']); ?>">
        <div class="img table">
            <div class="cell-middle">
                <img src="/dir.gif" alt="<?= $arSection['NAME'] ?>">
            </div>
        </div>
        <div class="name"><?= $arSection['NAME'] ?></div>
        <div class="size"></div>
    </a>

    <?
}
?>