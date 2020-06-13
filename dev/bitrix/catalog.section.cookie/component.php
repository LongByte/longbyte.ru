<?

$arIDs = array(-1);

if (!empty($_COOKIE["cur_cart"])) {
    $cookie = $_COOKIE["cur_cart"];
    $arCart = json_decode($cookie, true);
}

if (isset($arParams["ID"]) && isset($arParams["QUANTITY"])) {
    if (!isset($arCart[$arParams["ID"]])) {
        $arCart[$arParams["ID"]]["QUANTITY"] = $arParams["QUANTITY"];
    } else {
        $arCart[$arParams["ID"]]["QUANTITY"] = $arParams["QUANTITY"];
    }
}
$arResult = array("ITEMS" => array(), "TOTAL" => 0);
if (CModule::IncludeModule("iblock") && is_array($arCart) && count($arCart) > 0) {
    $rsItems = CIBlockElement::GetList(
            array(), array("IBLOCK_ID" => 5, "ACTIVE" => "Y", "ID" => array_keys($arCart)), false, false, array("ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "PREVIEW_TEXT")
    );
    while ($obItem = $rsItems->GetNextElement()) {
        $arItem = $obItem->GetFields();
        $arItem["PROPERTIES"] = $obItem->GetProperties();
        $img = CFile::ResizeImageGet($arItem["PREVIEW_PICTURE"], array("width" => 160, "height" => 240), BX_RESIZE_IMAGE_PROPORTIONAL, true);
        if ($img) {
            $arItem["PREVIEW_PICTURE"] = $img;
        }
        $arItem["QUANTITY"] = $arCart[$arItem["ID"]]["QUANTITY"];
        $arItem["FASOVKA"] = $arCart[$arItem["ID"]]["FASOVKA"];
        $price = $arItem["PROPERTIES"]["PRICE"]["VALUE"][0];
        if (count($arItem["PROPERTIES"]["PRICE"]["VALUE"]) > 1 && !empty($arItem["FASOVKA"])) {
            foreach ($arItem["PROPERTIES"]["PRICE"]["VALUE"] as $i => $pr) {
                if ($arItem["FASOVKA"] == $arItem["PROPERTIES"]["PRICE"]["DESCRIPTION"][$i]) {
                    $price = $pr;
                    break;
                }
            }
        }
        $arItem["PRICE"] = $price;
        $arResult["ITEMS"][] = $arItem;
        $arResult["TOTAL"] += $arItem["PRICE"] * $arItem["QUANTITY"];
    }
}
$this->IncludeComponentTemplate();
?>