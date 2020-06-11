<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

Bitrix\Main\Page\Asset::getInstance()->addString('<script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>', true);
Bitrix\Main\Page\Asset::getInstance()->addString('<meta name="robots" content="noindex, nofollow"/>', true);

$APPLICATION->IncludeComponent("longbyte:vue", "sensorsedit", Array(
    'STYLE_TO_COMPILER' => 'Y',
    ), $component->__parent
);
