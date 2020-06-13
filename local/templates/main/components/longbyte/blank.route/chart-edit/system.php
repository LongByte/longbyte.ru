<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$APPLICATION->IncludeComponent(
    "longbyte:blank",
    "system",
    Array(
        'SYSTEM_XML_ID' => $arResult['VARIABLES']['SYSTEM_XML_ID']
    ),
    $component
);
