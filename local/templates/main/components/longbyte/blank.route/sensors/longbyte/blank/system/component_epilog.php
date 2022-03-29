<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/axios.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/moment.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/Chart.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/vue-chartjs.min.js');
Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/vuejs-datepicker.min.js');

Asset::getInstance()->addString('<meta name="robots" content="noindex, nofollow"/>', true);

$APPLICATION->IncludeComponent("longbyte:vue", "sensors", array(
    'INCLUDE_COMPONENTS' => array('sensorbar', 'sensorline', 'sensorbool'),
    'STYLE_TO_COMPILER' => 'Y',
), $component->getParent()
);
