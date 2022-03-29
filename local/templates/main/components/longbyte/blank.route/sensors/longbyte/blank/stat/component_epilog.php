<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/axios.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/Chart.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/vue-chartjs.min.js');

Bitrix\Main\Page\Asset::getInstance()->addString('<meta name="robots" content="noindex, nofollow"/>', true);

$APPLICATION->IncludeComponent("longbyte:vue", "sensorsstat", array(
    'INCLUDE_COMPONENTS' => array('sensorline'),
    'STYLE_TO_COMPILER' => 'Y',
), $component->getParent()
);
