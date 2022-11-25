<?

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Редактирование результатов");
?><?

$APPLICATION->IncludeComponent(
    "longbyte:blank.route",
    "chart-edit",
    array(
        'SEF_MODE' => 'Y',
        "SEF_FOLDER" => "/chart/admin/",
        'SEF_URL_TEMPLATES' => array(
            "default" => "",
            "system" => "#SYSTEM_XML_ID#/",
        ),
    )
);
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>