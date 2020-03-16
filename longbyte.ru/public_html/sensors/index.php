<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Сенсоры");
?><?

$APPLICATION->IncludeComponent(
    "longbyte:blank.route",
    "sensors",
    Array(
        'SEF_MODE' => 'Y',
        "SEF_FOLDER" => "/sensors/",
        'SEF_URL_TEMPLATES' => array(
            "default" => "",
            "system" => "#SYSTEM_NAME#-#SYSTEM_TOKEN#/",
        )
    )
);
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>