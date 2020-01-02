<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
?>
<? if ($arResult['FORMAT'] == 'json'): ?>
    <?
    /* @var $APPLICATION \CMain */
    $APPLICATION->RestartBuffer();
    echo \LongByte\Vue::toJson($arResult['VUE']);
    die;
    ?>
<? elseif ($arResult['FORMAT'] == 'html'): ?>
    <script>
        window.vueData.system = <?= \LongByte\Vue::toVueJson($arResult['VUE']) ?>;
    </script>
<? endif; ?>