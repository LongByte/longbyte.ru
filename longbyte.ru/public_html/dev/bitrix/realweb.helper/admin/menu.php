<?

if (!CModule::IncludeModule('realweb.helper')) {
    return;
}


IncludeModuleLangFile(__FILE__);

if ($APPLICATION->GetGroupRight("search") != "D") {
    $aMenu = array(
        array(
            "parent_menu" => "global_menu_settings",
            "section" => "search",
            "sort" => 901,
            "text" => 'Поисковая оптимизация (дополнительно)',
            "title" => 'Поисковая оптимизация (дополнительно)',
            "icon" => "seo_menu_icon",
            "page_icon" => "seo_page_icon",
            "module_id" => "seo",
            "items_id" => "menu_seo",
            "items" => array(),
        )
    );

    $aMenu[0]['items'][] = array(
        "url" => "/bitrix/admin/realweb_sitemap.php?lang=" . LANGUAGE_ID,
        "more_url" => array("/bitrix/admin/realweb_sitemap_edit.php?lang=" . LANGUAGE_ID),
        "text" => 'Расширенная настройка sitemap.xml',
    );
    return $aMenu;
}
return false;
?>
