<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$APPLICATION->IncludeComponent(
    "longbyte:longbyte.csscompiler.template", "less", array(
    'TEMPLATE_PATH' => __DIR__ . '/',
), false, array(
        "HIDE_ICONS" => "Y",
    )
);
