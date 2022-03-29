<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/axios.min.js');
Asset::getInstance()->addString('<meta name="robots" content="noindex, nofollow"/>', true);

$APPLICATION->IncludeComponent("longbyte:vue", "sensorsedit", array(
    'INCLUDE_COMPONENTS' => array('sensorsedit-item', 'sensorsedit-device'),
    'STYLE_TO_COMPILER' => 'Y',
), $component->getParent()
);
