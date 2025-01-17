<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arType = array("page" => GetMessage("MAIN_INCLUDE_PAGE"), "sect" => GetMessage("MAIN_INCLUDE_SECT"));
if ($GLOBALS['USER']->CanDoOperation('edit_php')) {
    $arType["file"] = GetMessage("MAIN_INCLUDE_FILE");
}

$site_template = false;
$site = ($_REQUEST["site"] <> '' ? $_REQUEST["site"] : ($_REQUEST["src_site"] <> '' ? $_REQUEST["src_site"] : false));
if ($site !== false) {
    $rsSiteTemplates = CSite::GetTemplateList($site);
    while ($arSiteTemplate = $rsSiteTemplates->Fetch()) {
        if (strlen($arSiteTemplate["CONDITION"]) <= 0) {
            $site_template = $arSiteTemplate["TEMPLATE"];
            break;
        }
    }
}
if (CModule::IncludeModule('fileman')) {
    $arTemplates = CFileman::GetFileTemplates(LANGUAGE_ID, array($site_template));
    $arTemplatesList = array();
    foreach ($arTemplates as $key => $arTemplate) {
        $arTemplateList[$arTemplate["file"]] = "[" . $arTemplate["file"] . "] " . $arTemplate["name"];
    }
} else {
    $arTemplatesList = array("page_inc.php" => "[page_inc.php]", "sect_inc.php" => "[sect_inc.php]");
}

$arComponentParameters = array(
    "GROUPS" => array(
        "PARAMS" => array(
            "NAME" => GetMessage("MAIN_INCLUDE_PARAMS"),
        ),
    ),

    "PARAMETERS" => array(),
);

$arComponentParameters["PARAMETERS"]["CODE"] = array(
    "NAME" => GetMessage("MAIN_INCLUDE_CODE"),
    "TYPE" => "STRING",
    "DEFAULT" => "",
    "PARENT" => "PARAMS",
);

$arComponentParameters["PARAMETERS"]["EDIT_TEMPLATE"] = array(
    "NAME" => GetMessage("MAIN_INCLUDE_EDIT_TEMPLATE"),
    "TYPE" => "LIST",
    "VALUES" => $arTemplateList,
    "DEFAULT" => "",
    "ADDITIONAL_VALUES" => "Y",
    "PARENT" => "PARAMS",
);
?>