<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

foreach ($arResult['sections'] as $arSection) {
    ?>
    <a class="item" href="<?= $arSection['section_page_url'] ?>">
        <div class="img table">
            <div class="cell-middle">
                <img src="<?= $templateFolder ?>/../../../images/dir.gif" alt="<?= $arSection['name'] ?>">
            </div>
        </div>
        <div class="name"><?= $arSection['name'] ?></div>
        <div class="size"></div>
    </a>

    <?
}
