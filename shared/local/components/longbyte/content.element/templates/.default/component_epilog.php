<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var array $templateData */
/** @var @global CMain $APPLICATION */
global $APPLICATION;

if (strlen($arResult['REDIRECT']) > 0) {
    LocalRedirect($arResult['REDIRECT'], false, "301 Moved permanently");
}

$COMPONENTS = array();
foreach ($arResult['PROPERTIES']['PAGE_TYPE']['VALUE'] as $PAGE_TYPE) {
    if ($PAGE_TYPE['TYPE'] == 'COMPONENT') {
        $COMPONENTS[] = $PAGE_TYPE;
    }
}

$APPLICATION->SetPageProperty('COMPONENTS', $COMPONENTS);

$arPageProps = array();
foreach ($arResult['PROPERTIES']['PAGE_PROPS']['VALUE'] as $keyValue => $value) {
    $arPageProps[$value] = $arResult['PROPERTIES']['PAGE_PROPS']['DESCRIPTION'][$keyValue];
}

$APPLICATION->SetPageProperty('PAGE_PROPS', $arPageProps);
?>

