<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$arNewResult = array();
foreach ($arResult as $arItem) {
    $lastLevel1 = count($arNewResult) - 1;
    $lastLevel2 = count($arNewResult[$lastLevel1]["ITEMS"]) - 1;
    switch ($arItem["DEPTH_LEVEL"]) {
        case 1:
            $arNewResult[] = $arItem;
            break;
        case 2:
            $arNewResult[$lastLevel1]["ITEMS"][] = $arItem;
            break;
        case 3:
            $arNewResult[$lastLevel1]["ITEMS"][$lastLevel2]["ITEMS"][] = $arItem;
            break;
    }
    
}

$arResult = $arNewResult;
?>