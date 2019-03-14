<?

use \Bitrix\Main\Loader;
use \Bitrix\Main\EventManager;

Loader::registerAutoLoadClasses(null, array(
    '\LongByte\Wakeup' => '/local/php_interface/lib/Wakeup.php',
    'Site' => '/local/php_interface/classes/Site.php',
    '\Realweb\Migration\Iblock' => '/local/php_interface/classes/Migration/Iblock.php',
    '\Realweb\Builder\IblockBuilder' => '/local/php_interface/classes/Realweb/Builder/IblockBuilder.php',
    '\Realweb\Builder\UserField' => '/local/php_interface/classes/Realweb/Builder/UserField.php',
    '\Realweb\Builder\EventsBuilder' => '/local/php_interface/classes/Realweb/Builder/EventsBuilder.php',
    '\Realweb\Builder\FormBuilder' => '/local/php_interface/classes/Realweb/Builder/FormBuilder.php',
    '\Realweb\Builder\HLBuilder' => '/local/php_interface/classes/Realweb/Builder/HLBuilder.php',
));

EventManager::getInstance()->addEventHandler('main', 'OnPageStart', 'onPageStart');
EventManager::getInstance()->addEventHandler('iblock', 'OnIBlockPropertyBuildList', array('PageType', 'GetUserTypeDescription'));

function onPageStart() {
    Site::definders();
}
