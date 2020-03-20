<?

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\EventManager;

Loader::registerAutoLoadClasses(null, array(
    '\LongByte\Wakeup' => '/local/php_interface/classes/Longbyte/Wakeup.php',
    '\LongByte\Babel' => '/local/php_interface/classes/Longbyte/Babel.php',
    '\LongByte\Vue' => '/local/php_interface/classes/Longbyte/Vue.php',
    '\LongByte\Webp' => '/local/php_interface/classes/Longbyte/Webp.php',
    'Site' => '/local/php_interface/classes/Site.php',
    //Migration
    '\Migration\Builder\IblockBuilder' => '/local/php_interface/classes/Migration/Builder/IblockBuilder.php',
    '\Migration\Builder\UserField' => '/local/php_interface/classes/Migration/Builder/UserField.php',
    '\Migration\Builder\EventsBuilder' => '/local/php_interface/classes/Migration/Builder/EventsBuilder.php',
    '\Migration\Builder\FormBuilder' => '/local/php_interface/classes/Migration/Builder/FormBuilder.php',
    '\Migration\Builder\HLBuilder' => '/local/php_interface/classes/Migration/Builder/HLBuilder.php',
    //orm
    '\Bitrix\Iblock\ElementPropertyTable' => '/local/php_interface/lib/elementproperty.php',
    //telegram
    '\LongByte\Telegram\SessionTable' => '/local/php_interface/classes/Telegram/Longbyte/SessionTable.php',
    '\LongByte\Telegram\Bot' => '/local/php_interface/classes/Telegram/Longbyte/Bot.php',
    //api
    '\Api\Sensors\Get' => '/local/php_interface/classes/Api/Sensors/Get.php',
    '\Api\Sensors\Post' => '/local/php_interface/classes/Api/Sensors/Post.php',
));

EventManager::getInstance()->addEventHandler('main', 'OnPageStart', array('Site', 'onPageStart'));
EventManager::getInstance()->addEventHandler('main', 'onEpilog', array('Site', 'onEpilog'));
EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', array('Site', 'OnEndBufferContent'));
EventManager::getInstance()->addEventHandler('main', 'OnEndBufferContent', array('\LongByte\Webp', 'convertAllToWebp'));

include_once(Application::getDocumentRoot() . '/local/php_interface/lib/Api/Autoloader.php');
