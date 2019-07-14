<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$APPLICATION->IncludeComponent("longbyte:vue", "portfolio-list", Array(
    'INCLUDE_COMPONENTS' => array('portfolio-item', 'portfolio-item-tag'),
    'STYLE_TO_COMPILER' => 'Y'
    ), false
);
?>