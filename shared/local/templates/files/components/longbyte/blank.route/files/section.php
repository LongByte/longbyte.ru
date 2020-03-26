<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
?>
<div class="list">
    <?
    $APPLICATION->IncludeComponent(
        "longbyte:blank", "section", array(
        'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE']
        ), $component, array("HIDE_ICONS" => "Y")
    );
    ?>
</div>