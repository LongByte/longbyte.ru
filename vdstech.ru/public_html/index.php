<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("keywords", "vdstech, облако, облачные технологии, сервер, аренда сервера, 1с, сервер терминалов, удаленная рабочая станция, виртуальный сервер, хостинг");
$APPLICATION->SetPageProperty("description", "VDSTech - облачные технологии. Аренда виртуальных серверов под 1С, сервера терминалов, рабочие станции и хостинг");
$APPLICATION->SetTitle("VDSTech - облачные технологии");
?>
<div class="row">
    <div class="col-xs-12 text-center" style="height: 80px;">
        <h2>Выберите услугу:</h2>
    </div>
    <div class="col-xs-12 flex">
        <a class="col-xs-12 item" href="/vpn/">
            <div class="as-table">
                <div class="cell-middle">
                    Шифрованный VPN-канал
                    <br>
                    <span>от 200 рублей</span>
                </div>
            </div>
        </a>
        <a class="col-xs-12 col-sm-6 item" href="/cloud_1c/">
            <div class="as-table">
                <div class="cell-middle">
                    Облачная 1С-Бухгалтерия
                    <br>
                    <span>от 600 рублей</span>
                </div>
            </div>
        </a>
        <a class="col-xs-12 col-sm-6 item" href="/terminal_server/">
            <div class="as-table">
                <div class="cell-middle">
                    Сервер терминалов<br>
                    <span>(удаленных рабочих столов)</span>
                    <br>
                    <span>от 1500 рублей</span>
                </div>
            </div>
        </a>
        <a class="col-xs-12 col-sm-6 item" href="/cloud_workplace/">
            <div class="as-table">
                <div class="cell-middle">
                    Облачное рабочее место руководителя
                    <br>
                    <span>от 1000 рублей</span>
                </div>
            </div>
        </a>
        <a class="col-xs-12 col-sm-6 item" href="/web_hosting/">
            <div class="as-table">
                <div class="cell-middle">
                    Web-Хостинг
                    <br>
                    <span>от 150 рублей</span>
                </div>
            </div>
        </a>
        <a class="col-xs-12 item" href="/outsourcing/">
            <div class="as-table">
                <div class="cell-middle">
                    Outsourcing
                    <br>
                    <span>от 1500 рублей</span>
                </div>
            </div>
        </a>
    </div>
</div>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>