<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$APPLICATION->IncludeComponent("longbyte:vue", "system", Array(
    'INCLUDE_COMPONENTS' => array(),
    'STYLE_TO_COMPILER' => 'Y'
    ), false
);
?>