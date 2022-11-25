<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

if (!$arResult['root']) {
    ?>
    <a class="item" href="../">
        <div class="img table">
            <div class="cell-middle">
                <img src="<?= $templateFolder ?>/../../../images/dir.gif" alt="На уровень выше">
            </div>
        </div>
        <div class="name">..</div>
        <div class="size"></div>
    </a>
    <?
}
foreach ($arResult['elements'] as $key => $arItem) {
    ?>
    <a class="item" href="<?= $arItem['file_webp_src'] ?>" <? if ($arItem['is_image']): ?>rel="prettyPhoto[gal]" title="<a href='<?= $arItem['file_src'] ?>'>Прямая ссылка</a>"<? endif; ?>>
        <div class="img table">
            <div class="cell-middle">
                <img src="<?= $arItem['preview_picture'] ?>" alt="<?= $arItem['name'] ?>">
            </div>
        </div>
        <div class="name"><?= $arItem['name'] ?></div>
        <div class="size"><?= $arItem['file_size'] ?></div>
    </a>
    <?
}