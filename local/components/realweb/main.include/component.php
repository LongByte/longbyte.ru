<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Realweb\RealwebMainIncludeTable;

/* * ********************************************************************************************************* */
/*  Include Areas Component
  /* Params:
  /*	EDIT_MODE => {php | html | text} - default edit mode for an area. Default value - 'html'
  /*	EDIT_TEMPLATE => string - default template to add new area. Default value - page_inc.php / sect_inc.php
  /*
  /*********************************************************************************************************** */

//$arParams["EDIT_MODE"] = in_array($arParams["EDIT_MODE"], array("php", "html", "text")) ? $arParams["EDIT_MODE"] : "html";
$arParams["EDIT_TEMPLATE"] = strlen($arParams["EDIT_TEMPLATE"]) > 0 ? $arParams["EDIT_TEMPLATE"] : $arParams["AREA_FILE_SHOW"] . "_inc.php";

if (!CModule::IncludeModule('realweb.main.include')) {
    return;
}

$bRowFound = false;

if (strlen($arParams['CODE']) > 0) {
//Пробуем найти по CODE
    $res = RealwebMainIncludeTable::getByCode($arParams['CODE']);
    if ($row = $res->fetch()) {
        $bRowFound = true;
    }
}

if ($APPLICATION->GetShowIncludeAreas()) {
    //need fm_lpa for every .php file, even with no php code inside
    $bPhpFile = (!$GLOBALS["USER"]->CanDoOperation('edit_php') && in_array(GetFileExtension($sFileName), GetScriptFileExt()));

    $bCanEdit = $USER->CanDoFileOperation('fm_edit_existent_file', array(SITE_ID, $sFilePath . $sFileName)) && (!$bPhpFile || $GLOBALS["USER"]->CanDoFileOperation('fm_lpa', array(SITE_ID, $sFilePath . $sFileName)));
    $bCanAdd = $USER->CanDoFileOperation('fm_create_new_file', array(SITE_ID, $sFilePathTMP . $sFileName)) && (!$bPhpFile || $GLOBALS["USER"]->CanDoFileOperation('fm_lpa', array(SITE_ID, $sFilePathTMP . $sFileName)));

    if ($bCanEdit || $bCanAdd) {
        $editor = '&site=' . SITE_ID . '&back_url=' . urlencode($_SERVER['REQUEST_URI']) . '&templateID=' . urlencode(SITE_TEMPLATE_ID);

        if ($bRowFound) {
            if ($bCanEdit) {
                $arMenu = array();
                $arIcons = array(
                    array(
                        "URL" => 'javascript:' . $APPLICATION->GetPopupLink(
                                array(
                                    'URL' => "/bitrix/admin/main_include_public_edit.php?lang=" . LANGUAGE_ID . "&from=main.include&template=" . urlencode($arParams["EDIT_TEMPLATE"]) . "&CODE=" . urlencode($arParams['CODE']) . $editor,
                                    "PARAMS" => array(
                                        'width' => 770,
                                        'height' => 570,
                                        'resize' => true,
                                    ),
                                )
                            ),
                        "DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
                        "ICON" => "bx-context-toolbar-edit-icon",
                        "TITLE" => GetMessage("main_comp_include_edit"),
                        "ALT" => GetMessage("MAIN_INCLUDE_AREA_EDIT_" . $arParams["AREA_FILE_SHOW"]),
                        "MENU" => $arMenu,
                    ),
                );
            }
        } elseif ($bCanAdd) {
            $arMenu = array();
            $arIcons = array(
                array(
                    "URL" => 'javascript:' . $APPLICATION->GetPopupLink(
                            array(
                                'URL' => "/bitrix/admin/main_include_public_edit.php?lang=" . LANGUAGE_ID . "&from=main.include&CODE=" . urlencode($arParams['CODE']) . "&new=Y&template=" . urlencode($arParams["EDIT_TEMPLATE"]) . $editor,
                                "PARAMS" => array(
                                    'width' => 770,
                                    'height' => 570,
                                    'resize' => true,
                                    "dialog_type" => 'EDITOR',
                                    "min_width" => 700,
                                    "min_height" => 400,
                                ),
                            )
                        ),
                    "DEFAULT" => $APPLICATION->GetPublicShowMode() != 'configure',
                    "ICON" => "bx-context-toolbar-create-icon",
                    "TITLE" => GetMessage("main_comp_include_add1"),
                    "ALT" => GetMessage("MAIN_INCLUDE_AREA_ADD_" . $arParams["AREA_FILE_SHOW"]),
                    "MENU" => $arMenu,
                ),
            );
        }

        if (is_array($arIcons) && count($arIcons) > 0) {
            $this->AddIncludeAreaIcons($arIcons);
        }
    }
}

if ($bRowFound) {
    $arResult = $row;
    $this->IncludeComponentTemplate();
}
?>