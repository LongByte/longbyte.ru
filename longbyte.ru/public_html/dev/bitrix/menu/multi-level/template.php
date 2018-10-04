<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (!empty($arResult)): ?>
    <ul class="bottom-menu">
        <? foreach ($arResult as $arItem): ?>
            <li <?/* if ($arItem["SELECTED"]): ?>class="selected"<? endif;*/ ?>>
                <a href="<?= $arItem["LINK"] ?>">
                    <?= $arItem["TEXT"] ?>
                </a>
                <? if (isset($arItem["ITEMS"])): ?>
                    <ul class="sub-menu">
                        <? foreach ($arItem["ITEMS"] as $arSubItem): ?>
                            <li>
                                <a href="<?= $arSubItem["LINK"] ?>">
                                    <?= $arSubItem["TEXT"] ?>
                                </a>
                            </li>
                        <? endforeach ?>
                    </ul>
                <? endif; ?>
            </li>
        <? endforeach ?>
    </ul>
<? endif; ?>