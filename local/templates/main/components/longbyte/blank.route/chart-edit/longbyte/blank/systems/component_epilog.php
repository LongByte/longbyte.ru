<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

Bitrix\Main\Page\Asset::getInstance()->addString('<meta name="robots" content="noindex, nofollow"/>', true);

$APPLICATION->IncludeComponent("longbyte:vue", "systems", Array(
    'INCLUDE_COMPONENTS' => array(),
    'STYLE_TO_COMPILER' => 'Y'
    ), $component->__parent
);
?>