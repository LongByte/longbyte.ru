<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
if (!empty($arParams["FILTER_NAME"]) && count($arParams["ELEMENTS_ID"]) > 0) {
    $GLOBALS[$arParams["FILTER_NAME"]] = array("ID" => $arParams["ELEMENTS_ID"]);
}
?>