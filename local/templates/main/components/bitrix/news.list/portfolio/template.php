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
<div class="portfolio row">
    <?
    foreach ($arResult["ITEMS"] as &$arItem) {
        ?>
        <?
        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        if ($img = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE']['ID'], ['width' => 100, 'height' => 10000], BX_RESIZE_IMAGE_PROPORTIONAL)) {
            $arItem['PREVIEW_PICTURE']['SRC'] = $img['src'];
        }
        $startYear = $arItem['PROPERTIES']['YEAR_START']['VALUE'];
        $endYear = $arItem['PROPERTIES']['YEAR_FINISH']['VALUE'];
        ?>
        <div class="col-xs-12 col-md-6">
            <div class="item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
                <div class="img">
                    <img src="<?= $arItem['PREVIEW_PICTURE']['SRC'] ?>" />
                </div>
                <div class="desc">
                    <div class="name"><?= $arItem['NAME'] ?></div>
                    <div class="detail-text"><?= $arItem['DETAIL_TEXT'] ?></div>
                    <div class="preview-text"><?= $arItem['PREVIEW_TEXT'] ?></div>
                    <div class="years">
                        <?= $startYear ?> г.
                        <?= empty($endYear) || $endYear != $startYear ? '—' : '' ?>
                        <?= empty($endYear) ? 'н. в.' : ($endYear != $startYear ? $endYear . ' г.' : '' ) ?>
                    </div>
                    <div class="links">
                        <?
                        if (!empty($arItem['DETAIL_PICTURE']['SRC'])) {
                            ?>
                            <a href="<?= $arItem['DETAIL_PICTURE']['SRC'] ?>" target="_blank">Посмотреть макет</a>
                            <?
                        }
                        if (!empty($arItem['PROPERTIES']['URL']['VALUE'])) {
                            if (strpos($arItem['PROPERTIES']['URL']['VALUE'], 'http') !== 0)
                                $arItem['PROPERTIES']['URL']['VALUE'] = 'http://' . $arItem['PROPERTIES']['URL']['VALUE'];
                            ?>
                            <a href="<?= $arItem['PROPERTIES']['URL']['VALUE'] ?>" rel="nofollow" target="_blank">Перейти на сайт</a>
                            <?
                        }
                        ?>
                    </div>
                    <div class="tags">
                        <? foreach (explode(',', $arItem['TAGS']) as $tag) { ?>
                            <span><?= trim($tag) ?></span>
                        <? } ?>
                    </div>
                </div>
            </div>
        </div>
        <?
    }
    unset($arItem);
    ?>
</div>