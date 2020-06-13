<?
if (!CModule::IncludeModule('realweb.main.include')) {
    return;
}
use Bitrix\Main\Localization\Loc;
Loc::loadLanguageFile(__FILE__);
$aMenu[] = array( 
    "parent_menu" => "global_menu_content",
    "section" => "realweb_main_include",
    "sort" => 700,
    "text" => Loc::getMessage("REALWEB.MAIN.INCLUDE.SEPARATOR"),
    "title" => Loc::getMessage("REALWEB.MAIN.INCLUDE.SETTINGS_TITLE"),
    "icon" => "",
    "page_icon" => "",
    "items_id" => "menu_realweb_main_include",
    "module_id" => "realweb.main.include",
    "url" => "main_include_list.php?lang=".LANGUAGE_ID,
    "more_url" => array(
        "main_include_list.php",
        "main_include_edit.php",
    ),
);
return $aMenu;
?>
