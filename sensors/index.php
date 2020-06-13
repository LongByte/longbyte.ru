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
            "debug" => "debug/",
            "system" => "#SYSTEM_NAME#-#SYSTEM_TOKEN#/",
            "edit" => "#SYSTEM_NAME#-#SYSTEM_TOKEN#/edit/",
            "stat" => "#SYSTEM_NAME#-#SYSTEM_TOKEN#/stat/",
        )
    )
);
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>