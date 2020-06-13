<?

$rsAddress = CIBlockElement::GetList(
    array(), 
    array("IBLOCK_ID" => 11, "ACTIVE" => "Y"), 
    false, 
    false, 
    array("ID", "IBLOCK_ID", "NAME", "PREVIEW_TEXT")
);

$arResult["ADDRESS"] = array();
while ($arAddress = $rsAddress->GetNext()) {
    $arResult["ADDRESS"][] = strip_tags($arAddress["PREVIEW_TEXT"]);
}

?>