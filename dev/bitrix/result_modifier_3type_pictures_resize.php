<?

if ($arResult['DETAIL_PICTURE'] && is_array($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"])) {
    array_unshift($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"], $arResult['DETAIL_PICTURE']);
}

foreach ($arResult["PROPERTIES"]["MORE_PHOTO"]["VALUE"] as $value) {

    $arFull = CFile::GetFileArray($value);

    $arMiddle = CFile::ResizeImageGet(
            $value, array("width" => 360, "height" => 455), BX_RESIZE_IMAGE_PROPORTIONAL, true
    );

    $arPreview = CFile::ResizeImageGet(
            $value, array("width" => 115, "height" => 125), BX_RESIZE_IMAGE_PROPORTIONAL, true
    );

    $arResult["IMAGES"][] = array(
        "FULL" => $arFull["SRC"],
        "MIDDLE" => $arMiddle["src"],
        "PREVIEW" => $arPreview["src"]
    );
}
?>