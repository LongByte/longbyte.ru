<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (strlen($arResult['REDIRECT']) > 0) {
    LocalRedirect($arResult['REDIRECT'], false, "301 Moved permanently");
}
?>