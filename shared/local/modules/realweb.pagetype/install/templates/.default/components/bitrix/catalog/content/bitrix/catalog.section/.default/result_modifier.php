<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();


$arResult['REDIRECT'] = "";
if (intval($arResult['UF_REDIRECT']) > 0) {
    foreach ($arResult['ITEMS'] as $arItem) {
        $arResult['REDIRECT'] = $arItem['DETAIL_PAGE_URL'];
        break;
    }
}

$this->__component->SetResultCacheKeys(array('REDIRECT'));
