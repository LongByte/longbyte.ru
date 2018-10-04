<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var array $arCurrentValues */
/** @global CUserTypeManager $USER_FIELD_MANAGER */
if (!\Bitrix\Main\Loader::includeModule("iblock"))
    return;

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlock = array();
$rsIBlock = CIBlock::GetList(array("sort" => "asc"), array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE" => "Y"));
while ($arr = $rsIBlock->Fetch())
    $arIBlock[$arr["ID"]] = "[" . $arr["ID"] . "] " . $arr["NAME"];

$arProperty = array();
$arProperty_LNS = array();
$arProperty_N = array();
$arProperty_X = array();
$arSections = array();
$arElements = array();
if (0 < intval($arCurrentValues['IBLOCK_ID'])) {

    $rsSections = CIBlockSection::GetList(
            array("MARGIN_LEFT" => "ASC"), array(
            "IBLOCK_ID" => $arCurrentValues['IBLOCK_ID'],
            "ACTIVE" => "Y",
            "GLOBAL_ACTIVE" => "Y"
            ), false, array("ID", "NAME", "DEPTH_LEVEL")
    );

    while ($arSect = $rsSections->GetNext()) {
        $arSections[$arSect["ID"]] = str_repeat(" .  ", $arSect["DEPTH_LEVEL"] - 1) . $arSect["NAME"];
    }

    $rsElements = CIBlockElement::GetList(
            array(
            "IBLOCK_SECTION_ID" => "ASC",
            "NANE" => "ASC"
            ), array(
            "IBLOCK_ID" => $arCurrentValues['IBLOCK_ID'],
            "ACTIVE" => "Y",
            "SECTION_ID" => $arCurrentValues['SECTION_ID'],
            "SECTION_ACTIVE" => "Y",
            "SECTION_GLOBAL_ACTIVE" => "Y",
            "INCLUDE_SUBSECTIONS" => "Y"
            ), false, false, array("ID", "NAME", "IBLOCK_SECTION_ID")
    );

    while ($arEl = $rsElements->GetNext()) {
        $arElements[(string)$arEl["ID"]] = count($arCurrentValues['SECTION_ID']) != 1 ? $arSections[$arEl["IBLOCK_SECTION_ID"]] . " > " . $arEl["~NAME"] : $arEl["~NAME"];
    }
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "AJAX_MODE" => array(),
        "IBLOCK_TYPE" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlockType,
            "REFRESH" => "Y",
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_IBLOCK"),
            "TYPE" => "LIST",
            "ADDITIONAL_VALUES" => "Y",
            "VALUES" => $arIBlock,
            "REFRESH" => "Y",
        ),
        "SECTION_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_SECTION_ID"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arSections,
            "REFRESH" => "Y"
        ),
        "ELEMENTS_ID" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("IBLOCK_ELEMENTS_ID"),
            "TYPE" => "LIST",
            "MULTIPLE" => "Y",
            "VALUES" => $arElements,
        ),
        "FILTER_NAME" => array(
            "PARENT" => "DATA_SOURCE",
            "NAME" => GetMessage("IBLOCK_FILTER_NAME_IN"),
            "TYPE" => "STRING",
            "DEFAULT" => "",
        ),
    ),
);
?>