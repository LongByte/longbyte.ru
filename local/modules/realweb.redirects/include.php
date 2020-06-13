<?php

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

$strPath2Lang = str_replace('\\', '/', __FILE__);
$strPath2Lang = substr($strPath2Lang, 0, strlen($strPath2Lang) - strlen('/include.php'));

if (ModuleManager::isModuleInstalled('realweb.redirects')) {
    Loader::registerAutoLoadClasses('realweb.redirects', array(
        '\Realweb\Redirects\Redirects' => 'lib/Redirects.php',
    ));
}
