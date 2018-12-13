<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$INCLUDE_TEMPLATE = true;
if(isset($arParams['SITE_ID']) && strlen($arParams['SITE_ID']) > 0){
    $INCLUDE_TEMPLATE = false;
    if(SITE_ID == $arParams['SITE_ID']){
        $INCLUDE_TEMPLATE = true;
    }
}

if($INCLUDE_TEMPLATE){
    $this->IncludeComponentTemplate();
}

?>