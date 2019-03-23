<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Context;

global $APPLICATION;

$arParams['INCLUDE_COMPONENTS'] = is_array($arParams['INCLUDE_COMPONENTS']) ? $arParams['INCLUDE_COMPONENTS'] : array();
$arParams['STYLE_TO_COMPILER'] = $arParams['STYLE_TO_COMPILER'] == 'Y' ? 'Y' : 'N';
$obServer = Context::getCurrent()->getServer();

if (count($arParams['INCLUDE_COMPONENTS']) > 0) {
    foreach ($arParams['INCLUDE_COMPONENTS'] as $strComponent) {
        $APPLICATION->IncludeComponent("longbyte:vue", $strComponent, Array(
            'STYLE_TO_COMPILER' => $arParams['STYLE_TO_COMPILER']
            ), $this->__parent ?: $component
        );
    }
}

$this->IncludeComponentTemplate();

$strVueMask = $this->__template->__folder . '/*.vue';
$arVueFiles = glob($obServer->getDocumentRoot() . $strVueMask);
foreach ($arVueFiles as $strVueFile) {
    include_once $strVueFile;
}

$arStyleExt = array(
    'sass',
    'less'
);

if ($arParams['STYLE_TO_COMPILER'] == 'Y') {
    foreach ($arStyleExt as $ext) {
        $strFile = $this->__template->__folder . '/style.' . $ext;
        if (file_exists($obServer->getDocumentRoot() . $strFile)) {
            
            $jsonStylesToCompile = $APPLICATION->GetProperty('STYLE_TO_COMPILER', '');
            if ($jsonStylesToCompile == '') {
                $arStylesToCompile = array();
            } else {
                $arStylesToCompile = json_decode($jsonStylesToCompile, true);
            }

            $arStylesToCompile[] = $strFile;
            $APPLICATION->SetPageProperty('STYLE_TO_COMPILER', json_encode($arStylesToCompile));
            break;
        }
    }
}

?>