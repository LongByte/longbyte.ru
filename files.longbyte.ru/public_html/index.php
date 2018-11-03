<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Файлопомойка");
?><?

$APPLICATION->IncludeComponent(
    "bitrix:catalog", "files", Array(
    "ACTION_VARIABLE" => "action",
    "ADD_ELEMENT_CHAIN" => "N",
    "ADD_PICT_PROP" => "-",
    "ADD_PROPERTIES_TO_BASKET" => "Y",
    "ADD_SECTIONS_CHAIN" => "Y",
    "AJAX_MODE" => "N",
    "AJAX_OPTION_ADDITIONAL" => "",
    "AJAX_OPTION_HISTORY" => "N",
    "AJAX_OPTION_JUMP" => "N",
    "AJAX_OPTION_STYLE" => "Y",
    "BASKET_URL" => "/personal/basket.php",
    "CACHE_FILTER" => "N",
    "CACHE_GROUPS" => "Y",
    "CACHE_TIME" => "3600",
    "CACHE_TYPE" => "A",
    "COMMON_ADD_TO_BASKET_ACTION" => "ADD",
    "COMMON_SHOW_CLOSE_POPUP" => "N",
    "COMPATIBLE_MODE" => "Y",
    "CONVERT_CURRENCY" => "N",
    "DETAIL_ADD_DETAIL_TO_SLIDER" => "N",
    "DETAIL_ADD_TO_BASKET_ACTION" => array(0 => "BUY",),
    "DETAIL_BACKGROUND_IMAGE" => "-",
    "DETAIL_BRAND_USE" => "N",
    "DETAIL_BROWSER_TITLE" => "-",
    "DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
    "DETAIL_DETAIL_PICTURE_MODE" => "IMG",
    "DETAIL_DISPLAY_NAME" => "Y",
    "DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
    "DETAIL_META_DESCRIPTION" => "-",
    "DETAIL_META_KEYWORDS" => "-",
    "DETAIL_PROPERTY_CODE" => array("", ""),
    "DETAIL_SET_CANONICAL_URL" => "N",
    "DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
    "DETAIL_SHOW_MAX_QUANTITY" => "N",
    "DETAIL_STRICT_SECTION_CHECK" => "N",
    "DETAIL_USE_COMMENTS" => "N",
    "DETAIL_USE_VOTE_RATING" => "N",
    "DISABLE_INIT_JS_IN_COMPONENT" => "N",
    "DISPLAY_BOTTOM_PAGER" => "Y",
    "DISPLAY_TOP_PAGER" => "N",
    "ELEMENT_SORT_FIELD" => "sort",
    "ELEMENT_SORT_FIELD2" => "name",
    "ELEMENT_SORT_ORDER" => "asc",
    "ELEMENT_SORT_ORDER2" => "asc",
    "FILTER_VIEW_MODE" => "VERTICAL",
    "HIDE_NOT_AVAILABLE" => "N",
    "HIDE_NOT_AVAILABLE_OFFERS" => "N",
    "IBLOCK_ID" => "5",
    "IBLOCK_TYPE" => "files",
    "INCLUDE_SUBSECTIONS" => "A",
    "INSTANT_RELOAD" => "N",
    "LABEL_PROP" => "-",
    "LINE_ELEMENT_COUNT" => "1",
    "LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
    "LINK_IBLOCK_ID" => "",
    "LINK_IBLOCK_TYPE" => "",
    "LINK_PROPERTY_SID" => "",
    "LIST_BROWSER_TITLE" => "-",
    "LIST_META_DESCRIPTION" => "-",
    "LIST_META_KEYWORDS" => "-",
    "LIST_PROPERTY_CODE" => array("", "FILE", ""),
    "MESSAGE_404" => "",
    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
    "MESS_BTN_BUY" => "Купить",
    "MESS_BTN_COMPARE" => "Сравнение",
    "MESS_BTN_DETAIL" => "Подробнее",
    "MESS_NOT_AVAILABLE" => "Нет в наличии",
    "PAGER_BASE_LINK_ENABLE" => "N",
    "PAGER_DESC_NUMBERING" => "N",
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
    "PAGER_SHOW_ALL" => "N",
    "PAGER_SHOW_ALWAYS" => "N",
    "PAGER_TEMPLATE" => ".default",
    "PAGER_TITLE" => "Файлы",
    "PAGE_ELEMENT_COUNT" => "300",
    "PARTIAL_PRODUCT_PROPERTIES" => "N",
    "PRICE_CODE" => array(),
    "PRICE_VAT_INCLUDE" => "Y",
    "PRICE_VAT_SHOW_VALUE" => "N",
    "PRODUCT_ID_VARIABLE" => "id",
    "PRODUCT_PROPERTIES" => array(),
    "PRODUCT_PROPS_VARIABLE" => "prop",
    "PRODUCT_QUANTITY_VARIABLE" => "quantity",
    "SECTIONS_SHOW_PARENT_NAME" => "Y",
    "SECTIONS_VIEW_MODE" => "LINE",
    "SECTION_ADD_TO_BASKET_ACTION" => "ADD",
    "SECTION_BACKGROUND_IMAGE" => "-",
    "SECTION_COUNT_ELEMENTS" => "N",
    "SECTION_ID_VARIABLE" => "SECTION_ID",
    "SECTION_TOP_DEPTH" => "1",
    "SEF_FOLDER" => "/",
    "SEF_MODE" => "Y",
    "SEF_URL_TEMPLATES" => Array("compare" => "compare.php?action=#ACTION_CODE#", "element" => "#SECTION_CODE#/#ELEMENT_CODE#/", "section" => "#SECTION_CODE#/", "sections" => "", "smart_filter" => "#SECTION_ID#/filter/#SMART_FILTER_PATH#/apply/"),
    "SET_LAST_MODIFIED" => "Y",
    "SET_STATUS_404" => "Y",
    "SET_TITLE" => "Y",
    "SHOW_404" => "N",
    "SHOW_DEACTIVATED" => "N",
    "SHOW_DISCOUNT_PERCENT" => "N",
    "SHOW_OLD_PRICE" => "N",
    "SHOW_PRICE_COUNT" => "1",
    "SHOW_TOP_ELEMENTS" => "N",
    "SIDEBAR_DETAIL_SHOW" => "Y",
    "SIDEBAR_PATH" => "",
    "SIDEBAR_SECTION_SHOW" => "Y",
    "TEMPLATE_THEME" => "blue",
    "TOP_ADD_TO_BASKET_ACTION" => "ADD",
    "USER_CONSENT" => "N",
    "USER_CONSENT_ID" => "0",
    "USER_CONSENT_IS_CHECKED" => "Y",
    "USER_CONSENT_IS_LOADED" => "N",
    "USE_ALSO_BUY" => "N",
    "USE_BIG_DATA" => "N",
    "USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
    "USE_COMPARE" => "N",
    "USE_ELEMENT_COUNTER" => "Y",
    "USE_FILTER" => "N",
    "USE_GIFTS_DETAIL" => "N",
    "USE_GIFTS_MAIN_PR_SECTION_LIST" => "N",
    "USE_GIFTS_SECTION" => "N",
    "USE_MAIN_ELEMENT_SECTION" => "N",
    "USE_PRICE_COUNT" => "N",
    "USE_PRODUCT_QUANTITY" => "N",
    "USE_REVIEW" => "N",
    "USE_SALE_BESTSELLERS" => "N",
    "USE_STORE" => "N",
    "VARIABLE_ALIASES" => array("compare" => array("ACTION_CODE" => "action",),)
    )
);
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>