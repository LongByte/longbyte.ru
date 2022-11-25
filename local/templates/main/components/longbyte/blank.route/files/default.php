<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
?>
<div class="files-list">
    <?
    $APPLICATION->IncludeComponent(
        "longbyte:blank", "sections", array(), $component, array("HIDE_ICONS" => "Y")
    );
    ?>
    <?
    $APPLICATION->IncludeComponent(
        "longbyte:blank", "section", array(), $component, array("HIDE_ICONS" => "Y")
    );
    ?>
</div>