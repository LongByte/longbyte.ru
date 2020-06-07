<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use \Bitrix\Main\Page\Asset;

Asset::getInstance()->addString('<script src="//cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>', true);
Asset::getInstance()->addString('<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>', true);
Asset::getInstance()->addString('<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js"></script>', true);
Asset::getInstance()->addString('<script src="//unpkg.com/vue-chartjs/dist/vue-chartjs.min.js"></script>', true);
Asset::getInstance()->addString('<script src="//unpkg.com/vuejs-datepicker"></script>', true);
Asset::getInstance()->addString('<meta name="robots" content="noindex, nofollow"/>', true);

$APPLICATION->IncludeComponent("longbyte:vue", "sensors", Array(
    'INCLUDE_COMPONENTS' => array('sensorbar', 'sensorline', 'sensorbool'),
    'STYLE_TO_COMPILER' => 'Y',
    ), $component->__parent
);
