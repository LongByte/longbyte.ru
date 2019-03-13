<?

use \Bitrix\Main\Loader;
use \Bitrix\Main\EventManager;

Loader::registerAutoLoadClasses(null, array(
    '\LongByte\Wakeup' => '/local/php_interface/lib/Wakeup.php',
    'Site' => '/local/php_interface/classes/Site.php',
));

EventManager::getInstance()->addEventHandler('main', 'OnPageStart', 'onPageStart');
EventManager::getInstance()->addEventHandler('iblock', 'OnIBlockPropertyBuildList', array('PageType', 'GetUserTypeDescription'));

function onPageStart() {
    Site::definders();
}
