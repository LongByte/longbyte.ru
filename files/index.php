<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Файлопомойка");
?><?

$APPLICATION->IncludeComponent(
    "longbyte:blank.route", "files", array(
        "SEF_FOLDER" => "/files/",
        "SEF_MODE" => "Y",
        "SEF_URL_TEMPLATES" => array(
            "default" => "",
            "section" => "#SECTION_CODE#/",
        ),
    )
);
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>