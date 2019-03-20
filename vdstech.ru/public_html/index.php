<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "vdstech, облако, облачные технологии, сервер, аренда сервера, 1с, сервер терминалов, удаленная рабочая станция, виртуальный сервер, хостинг");
$APPLICATION->SetPageProperty("description", "VDSTech - облачные технологии. Аренда виртуальных серверов под 1С, сервера терминалов, рабочие станции и хостинг");
$APPLICATION->SetTitle("VDSTech - облачные технологии");
?>
<?

$APPLICATION->IncludeComponent(
    "longbyte:content.element", ".default", array(
    "COMPONENT_TEMPLATE" => ".default",
    "IBLOCK_TYPE" => "main",
    "IBLOCK_ID" => IBLOCK_MAIN_CONTENT,
    "ELEMENT_ID" => "",
    "ELEMENT_CODE" => "index",
    "SECTION_ID" => "",
    "SECTION_CODE" => "",
    "HIDE_NOT_AVAILABLE" => "N",
    "PROPERTY_CODE" => array(
        0 => "",
        1 => "",
    ),
    "OFFERS_LIMIT" => "0",
    "BACKGROUND_IMAGE" => "-",
    "TEMPLATE_THEME" => "",
    "ADD_PICT_PROP" => "-",
    "LABEL_PROP" => "-",
    "DISPLAY_NAME" => "N",
    "DETAIL_PICTURE_MODE" => "IMG",
    "ADD_DETAIL_TO_SLIDER" => "N",
    "DISPLAY_PREVIEW_TEXT_MODE" => "E",
    "PRODUCT_SUBSCRIPTION" => "N",
    "SHOW_DISCOUNT_PERCENT" => "N",
    "SHOW_OLD_PRICE" => "N",
    "SHOW_MAX_QUANTITY" => "N",
    "SHOW_CLOSE_POPUP" => "N",
    "MESS_BTN_BUY" => "Купить",
    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
    "MESS_BTN_SUBSCRIBE" => "Подписаться",
    "MESS_BTN_COMPARE" => "Сравнить",
    "MESS_NOT_AVAILABLE" => "Нет в наличии",
    "USE_VOTE_RATING" => "N",
    "USE_COMMENTS" => "N",
    "BRAND_USE" => "N",
    "SECTION_URL" => "",
    "DETAIL_URL" => "",
    "SECTION_ID_VARIABLE" => "SECTION_ID",
    "CHECK_SECTION_ID_VARIABLE" => "N",
    "SEF_MODE" => "N",
    "CACHE_TYPE" => "A",
    "CACHE_TIME" => "36000000",
    "CACHE_GROUPS" => "Y",
    "SET_TITLE" => "Y",
    "SET_CANONICAL_URL" => "N",
    "SET_BROWSER_TITLE" => "Y",
    "BROWSER_TITLE" => "-",
    "SET_META_KEYWORDS" => "Y",
    "META_KEYWORDS" => "-",
    "SET_META_DESCRIPTION" => "Y",
    "META_DESCRIPTION" => "-",
    "SET_LAST_MODIFIED" => "Y",
    "USE_MAIN_ELEMENT_SECTION" => "N",
    "ADD_SECTIONS_CHAIN" => "N",
    "ADD_ELEMENT_CHAIN" => "N",
    "ACTION_VARIABLE" => "action",
    "PRODUCT_ID_VARIABLE" => "id",
    "DISPLAY_COMPARE" => "N",
    "PRICE_CODE" => array(
    ),
    "USE_PRICE_COUNT" => "N",
    "SHOW_PRICE_COUNT" => "1",
    "PRICE_VAT_INCLUDE" => "Y",
    "PRICE_VAT_SHOW_VALUE" => "N",
    "CONVERT_CURRENCY" => "N",
    "BASKET_URL" => "/personal/basket.php",
    "USE_PRODUCT_QUANTITY" => "N",
    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
    "ADD_PROPERTIES_TO_BASKET" => "N",
    "PRODUCT_PROPS_VARIABLE" => "prop",
    "PARTIAL_PRODUCT_PROPERTIES" => "N",
    "PRODUCT_PROPERTIES" => array(
    ),
    "ADD_TO_BASKET_ACTION" => array(
        0 => "BUY",
    ),
    "LINK_IBLOCK_TYPE" => "",
    "LINK_IBLOCK_ID" => "",
    "LINK_PROPERTY_SID" => "",
    "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
    "USE_GIFTS_DETAIL" => "N",
    "USE_GIFTS_MAIN_PR_SECTION_LIST" => "N",
    "SET_STATUS_404" => "N",
    "SHOW_404" => "N",
    "MESSAGE_404" => "",
    "USE_ELEMENT_COUNTER" => "N",
    "SHOW_DEACTIVATED" => "N",
    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
    "SET_VIEWED_IN_COMPONENT" => "N"
    ), false
);
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>