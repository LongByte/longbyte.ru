<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

/** @var \CBitrixComponent $this */
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\IO;

$bReturn = $arParams['RETURN'] == 'Y';

if ($bReturn) {
    ob_start();
    $this->IncludeComponentTemplate();
    $strContent = ob_get_contents();
    ob_end_clean();
    return $strContent;
} else {
    $this->IncludeComponentTemplate();
}

if (Loader::includeModule('longbyte.compiler')) {

    $strTemplateFolder = $this->getTemplate()->GetFolder() . '/';
    $obStyleLess = new IO\File(Application::getDocumentRoot() . $strTemplateFolder . 'style.less');
    if ($obStyleLess->isExists()) {

        $APPLICATION->IncludeComponent(
            "longbyte:longbyte.csscompiler.template", "less", array(
            'TEMPLATE_PATH' => $strTemplateFolder
            ), false, array(
            "HIDE_ICONS" => "Y"
            )
        );
    }
}