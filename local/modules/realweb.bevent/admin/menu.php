<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;

global $USER;

if (!Loader::includeModule('realweb.bevent') || !$USER->IsAdmin()) {
    return;
}

Loc::loadLanguageFile(__FILE__);
$aMenu[] = array(
    'parent_menu' => 'global_menu_services',
    'section' => 'realweb_bevent',
    'sort' => 700,
    'text' => Loc::getMessage('REALWEB.BEVENT.SEPARATOR'),
    'title' => Loc::getMessage('REALWEB.BEVENT.SETTINGS_TITLE'),
    'icon' => '',
    'page_icon' => '',
    'items_id' => 'realweb_bevent',
    'module_id' => 'realweb.bevent',
    'url' => 'realweb_bevents_list.php?lang=' . LANGUAGE_ID,
    'more_url' => array(
        'realweb_bevents_list.php',
    ),
);
return $aMenu;
?>
