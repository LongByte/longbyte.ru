<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "VDSTech - Поддержка");
$APPLICATION->SetTitle("Поддержка");
?><main class="fade-bg shadow">
<div class="container">
	 <?$APPLICATION->IncludeComponent(
	"bitrix:iblock.element.add.form",
	"",
	Array(
		"CUSTOM_TITLE_DATE_ACTIVE_FROM" => "",
		"CUSTOM_TITLE_DATE_ACTIVE_TO" => "",
		"CUSTOM_TITLE_DETAIL_PICTURE" => "",
		"CUSTOM_TITLE_DETAIL_TEXT" => "",
		"CUSTOM_TITLE_IBLOCK_SECTION" => "",
		"CUSTOM_TITLE_NAME" => "",
		"CUSTOM_TITLE_PREVIEW_PICTURE" => "",
		"CUSTOM_TITLE_PREVIEW_TEXT" => "",
		"CUSTOM_TITLE_TAGS" => "",
		"DEFAULT_INPUT_SIZE" => "30",
		"DETAIL_TEXT_USE_HTML_EDITOR" => "Y",
		"ELEMENT_ASSOC" => "PROPERTY_ID",
		"ELEMENT_ASSOC_PROPERTY" => "",
		"GROUPS" => array("1"),
		"IBLOCK_ID" => "1",
		"IBLOCK_TYPE" => "news",
		"LEVEL_LAST" => "Y",
		"LIST_URL" => "",
		"MAX_FILE_SIZE" => "0",
		"MAX_LEVELS" => "100000",
		"MAX_USER_ENTRIES" => "100000",
		"PREVIEW_TEXT_USE_HTML_EDITOR" => "Y",
		"PROPERTY_CODES" => array("NAME","TAGS","DATE_ACTIVE_FROM","DATE_ACTIVE_TO","IBLOCK_SECTION","PREVIEW_TEXT","PREVIEW_PICTURE","DETAIL_TEXT","DETAIL_PICTURE"),
		"PROPERTY_CODES_REQUIRED" => array("NAME","TAGS","DATE_ACTIVE_FROM","DATE_ACTIVE_TO","IBLOCK_SECTION","PREVIEW_TEXT","PREVIEW_PICTURE","DETAIL_TEXT","DETAIL_PICTURE"),
		"RESIZE_IMAGES" => "Y",
		"SEF_MODE" => "N",
		"STATUS" => "ANY",
		"STATUS_NEW" => "N",
		"USER_MESSAGE_ADD" => "",
		"USER_MESSAGE_EDIT" => "",
		"USE_CAPTCHA" => "Y"
	)
);?>
</div>
 </main><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>