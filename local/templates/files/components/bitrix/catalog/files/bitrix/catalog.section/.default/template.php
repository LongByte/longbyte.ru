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

if (IntVal($arResult['DEPTH_LEVEL']) > 0) {
    ?>
    <a class="item" href="../" id="<?= $this->GetEditAreaId($arSection['ID']); ?>">
        <div class="img table">
            <div class="cell-middle">
                <img src="/dir.gif" alt="На уровень выше">
            </div>
        </div>
        <div class="name">..</div>
        <div class="size"></div>
    </a>
    <?
}
if (!empty($arResult['ITEMS'])) {
    foreach ($arResult['ITEMS'] as $key => $arItem) {
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], $strElementEdit);
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], $strElementDelete, $arElementDeleteParams);
        $strMainID = $this->GetEditAreaId($arItem['ID']);
        ?>
        <a class="item" href="<?= $arItem['FILE']['SRC'] ?>" <? if ($arItem['IS_IMG']): ?>rel="prettyPhoto[gal]" title="<a href='<?= $arItem['FILE']['SRC'] ?>'>Прямая ссылка</a>"<? endif; ?> id="<?= $strMainID; ?>">
            <div class="img table">
                <div class="cell-middle">
                    <img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" alt="<?= $arItem['NAME'] ?>">
                </div>
            </div>
            <div class="name"><?= $arItem['NAME'] ?></div>
            <div class="size"><?= $arItem['FILE']['FILE_SIZE'] ?></div>
        </a>
        <?
    }
}
?>