<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

global $APPLICATION;
global $arFilterContentMenuHeaderElement;
global $arFilterContentMenuHeaderSection;

$HEADER_ID_ELEMENT = IblockHelper::GetEnumIdByPropertyCode(IBLOCK_CONTENT_REALWEB_CONTENT, 'MENU', 'content');
$HEADER_ID_SECTION = IblockHelper::GetSectionEnumIdByPropertyCode(IBLOCK_CONTENT_REALWEB_CONTENT, 'UF_MENU', 'content');

$arFilterContentMenuHeaderElement = array(
    "PROPERTY_MENU" => $HEADER_ID_ELEMENT,
);

$arFilterContentMenuHeaderSection = array(
    "UF_MENU" => $HEADER_ID_SECTION,
);

$aMenuLinksExt = $APPLICATION->IncludeComponent(
    "realweb:menu.sections", "", array(
    "ID" => "",
    "IBLOCK_TYPE" => "content",
    "IBLOCK_ID" => IBLOCK_CONTENT_REALWEB_CONTENT,
    "SECTION_URL" => "",
    "CACHE_TIME" => "3600",
    "IS_SEF" => "Y",
    "SEF_BASE_URL" => "/",
    "SECTION_PAGE_URL" => "#SECTION_CODE_PATH#/",
    "DETAIL_PAGE_URL" => "#CODE#/",
    "DEPTH_LEVEL" => "2",
    "CACHE_TYPE" => "Y",
    "ELEMENT_FILTERNAME" => "arFilterContentMenuHeaderElement",
    "SECTION_FILTERNAME" => "arFilterContentMenuHeaderSection",
    "EXTERNAL_LINK" => array('PageType', 'GetExternalLink'),
    "EXTERNAL_LINK_PROPERTY" => 'PAGE_TYPE',
    ), false
);

$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);

