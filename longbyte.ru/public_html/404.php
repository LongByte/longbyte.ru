<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/urlrewrite.php');

CHTTP::SetStatus("404 Not Found");
@define("ERROR_404", "Y");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Страница не найдена");
?>
<br><h1 style="text-align: center">Страница не найдена</h1>
<p style="text-align: center">Такой страницы на сайте нет, возможно вы не правильно ввели адрес. Вы можете перейти на <a href="/">главную</a>.</p>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>