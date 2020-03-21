<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Портфолио");
?>
<h1 style="text-align: center;">Портфолио</h1>
<?
$APPLICATION->IncludeComponent("longbyte:blank", "portfolio", Array());
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>