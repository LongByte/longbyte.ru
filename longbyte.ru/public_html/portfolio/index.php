<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Портфолио");
?>
<?
$APPLICATION->IncludeComponent("longbyte:blank", "portfolio", Array());
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>