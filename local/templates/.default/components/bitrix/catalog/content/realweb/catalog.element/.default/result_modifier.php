<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
$arResult['TEXT_PAGE'] = false;
$arResult['REDIRECT'] = "";
foreach ($arResult['PROPERTIES']['PAGE_TYPE']['VALUE'] as $PAGE_TYPE) {
    if ($PAGE_TYPE['TYPE'] == 'PAGE') {
        $arResult['TEXT_PAGE'] = true;
    }
    if ($PAGE_TYPE['TYPE'] == 'EXTERNAL_LINK') {
        if ($PAGE_TYPE['LINK'] != $arResult['DETAIL_PAGE_URL']) {
            $arResult['REDIRECT'] = $PAGE_TYPE['LINK'];
        }
    }
}

$this->__component->SetResultCacheKeys(array('REDIRECT'));
