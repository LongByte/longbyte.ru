<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Page\Asset;

Asset::getInstance()->addString('<script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>', true);
Asset::getInstance()->addString('<meta name="robots" content="noindex, nofollow"/>', true);

$APPLICATION->IncludeComponent("longbyte:vue", "sensorsonline", Array(
//    'INCLUDE_COMPONENTS' => array('sensorbar', 'sensorline', 'sensorbool'),
    'STYLE_TO_COMPILER' => 'Y',
    ), $component->__parent
);
