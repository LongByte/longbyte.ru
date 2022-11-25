<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$APPLICATION->IncludeComponent(
    "longbyte:blank",
    "edit",
    array(
        'SYSTEM_NAME' => $arResult['VARIABLES']['SYSTEM_NAME'],
        'SYSTEM_TOKEN' => $arResult['VARIABLES']['SYSTEM_TOKEN'],
    ),
    $component
);
