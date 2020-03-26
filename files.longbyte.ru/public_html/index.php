<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Файлопомойка");
?><?

$APPLICATION->IncludeComponent(
    "longbyte:blank.route", "files", Array(
    "SEF_FOLDER" => "/",
    "SEF_MODE" => "Y",
    "SEF_URL_TEMPLATES" => Array(
        "default" => "",
        "section" => "#SECTION_CODE#/",
    ),
    )
);
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>