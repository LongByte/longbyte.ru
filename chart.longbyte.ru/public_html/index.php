<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Сравнительные тесты производительности компьютеров");
?>
<?
$APPLICATION->IncludeComponent(
    "longbyte:chart", "", Array()
);
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>