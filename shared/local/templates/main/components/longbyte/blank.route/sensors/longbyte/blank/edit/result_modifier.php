<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

$arResult['SYSTEM_NAME'] = $arParams['SYSTEM_NAME'];
$arResult['SYSTEM_TOKEN'] = $arParams['SYSTEM_TOKEN'];

\Api\Core\Main\Seo::getInstance()->setPageTitle($arResult['SYSTEM_NAME'] . ' - Сенсоры. Настройка.');