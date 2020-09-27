<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

/** @var \CUser $USER */
global $USER;

if ($USER->IsAdmin()) {

    $APPLICATION->IncludeComponent(
        "longbyte:blank",
        "online",
        Array(
            'SYSTEM_NAME' => $arResult['VARIABLES']['SYSTEM_NAME'],
            'SYSTEM_TOKEN' => $arResult['VARIABLES']['SYSTEM_TOKEN']
        ),
        $component
    );
} else {
    \Api\Core\Main\NotFound::setStatus404();
}