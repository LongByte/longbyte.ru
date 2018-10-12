<?

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CAdminMenu $this
 */
use \Bitrix\Main\Localization\Loc;

if ($APPLICATION->GetGroupRight("seo") > "D" && $APPLICATION->GetGroupRight("longbyte.sitemap") > "D") {
    if (\Bitrix\Main\ModuleManager::isModuleInstalled('seo') && \Bitrix\Main\ModuleManager::isModuleInstalled('longbyte.sitemap')) {
        IncludeModuleLangFile(__FILE__);

        $aMenu = array(
            array(
                "parent_menu" => "global_menu_marketing",
                "sort" => 901,
                "url" => "longbyte_sitemap.php?lang=" . LANGUAGE_ID,
                "more_url" => array("longbyte_sitemap_edit.php?lang=" . LANGUAGE_ID),
                "text" => Loc::getMessage("LONGBYTE_MENU_SITEMAP_ALT"),
            )
        );

        return $aMenu;
    }
}
return false;
