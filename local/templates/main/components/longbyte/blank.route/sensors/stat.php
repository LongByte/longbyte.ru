<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$obRequest = Bitrix\Main\Context::getCurrent()->getRequest();

$APPLICATION->IncludeComponent(
    "longbyte:blank",
    "stat",
    Array(
        'SYSTEM_NAME' => $arResult['VARIABLES']['SYSTEM_NAME'],
        'SYSTEM_TOKEN' => $arResult['VARIABLES']['SYSTEM_TOKEN'],
        'SINCE' => $obRequest->get('since'),
    ),
    $component
);
