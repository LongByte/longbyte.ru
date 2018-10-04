<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Сравнительные тесты производительности компьютеров");
?><h2 style="text-align: center;">Добро пожаловать на сборник результатов бенчмарков</h2>
<h3 style="text-align: center;">Выберите интересующий вид тестов</h3>
<br>
<?
$APPLICATION->IncludeComponent(
    "longbyte:chart", "", Array()
);
?><? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>