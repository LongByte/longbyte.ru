<?php

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen('/include.php'));

if (ModuleManager::isModuleInstalled('realweb.pagetype')) {
    Loader::registerAutoLoadClasses('realweb.pagetype', array(
        '\Realweb\PageType\PageType' => 'lib/PageType.php',
        '\Realweb\PageType\Handlers' => 'lib/Handlers.php',
    ));
}
