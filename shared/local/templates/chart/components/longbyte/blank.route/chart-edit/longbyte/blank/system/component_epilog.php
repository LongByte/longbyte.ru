<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

Bitrix\Main\Page\Asset::getInstance()->addString('<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>', true);

$APPLICATION->IncludeComponent("longbyte:vue", "system", Array(
    'INCLUDE_COMPONENTS' => array(),
    'STYLE_TO_COMPILER' => 'Y'
    ), false
);
?>