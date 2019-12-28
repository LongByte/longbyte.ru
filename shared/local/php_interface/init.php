<?

use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

Loader::registerAutoLoadClasses(null, array(
    '\LongByte\Wakeup' => '/local/php_interface/classes/Wakeup.php',
    '\LongByte\Babel' => '/local/php_interface/classes/Babel.php',
    '\LongByte\Vue' => '/local/php_interface/classes/Vue.php',
    'Site' => '/local/php_interface/classes/Site.php',
    //PageType
    'PageType' => '/local/php_interface/classes/PageType.php',
    //Migration
    '\Longbyte\Migration\Iblock' => '/local/php_interface/classes/Migration/Iblock.php',
    '\Longbyte\Builder\IblockBuilder' => '/local/php_interface/classes/Longbyte/Builder/IblockBuilder.php',
    '\Longbyte\Builder\UserField' => '/local/php_interface/classes/Longbyte/Builder/UserField.php',
    '\Longbyte\Builder\EventsBuilder' => '/local/php_interface/classes/Longbyte/Builder/EventsBuilder.php',
    '\Longbyte\Builder\FormBuilder' => '/local/php_interface/classes/Longbyte/Builder/FormBuilder.php',
    '\Longbyte\Builder\HLBuilder' => '/local/php_interface/classes/Longbyte/Builder/HLBuilder.php',
    //orm
    '\Bitrix\Iblock\ElementPropertyTable' => '/local/php_interface/lib/elementproperty.php',
    //telegram
    '\LongByte\Telegram\SessionTable' => '/local/php_interface/classes/Telegram/SessionTable.php',
    '\LongByte\Telegram\Bot' => '/local/php_interface/classes/Telegram/Bot.php',
));

EventManager::getInstance()->addEventHandler('main', 'OnPageStart', array('Site', 'onPageStart'));
EventManager::getInstance()->addEventHandler('iblock', 'OnIBlockPropertyBuildList', array('PageType', 'GetUserTypeDescription'));
EventManager::getInstance()->addEventHandler('main', 'onEpilog', array('Site', 'onEpilog'));
EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', array('Site', 'OnEndBufferContent'));
