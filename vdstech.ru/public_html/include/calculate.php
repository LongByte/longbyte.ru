<?
$APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.min.js');
$APPLICATION->AddHeadScript('/include/calculate.js');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.structure.min.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.theme.min.css');
$APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/calculate.css');
?>
<div autocomplete="off">
    <div class="row">
        <div class="col-xs-12">
            <div class="dialog-title h1">Калькулятор конфигурации сервера</div>
        </div>
    </div>
    <div class="row border">
        <div class="col-xs-12">
            Выберите сервер
        </div>
        <? /* <div class="col-xs-12 col-sm-10">
          <input type="radio" name="server" value="1" id="server1"><label for="server1">2 x Xeon X5355 (4 ядра, 2.66ГГц)</label><br>
          </div>
          <div class="col-sm-2 hidden-xs cpu-chart">
          <div class="" style="width: 38%" title="Наглядное сравнение производительности серверов"></div>
          </div> */ ?>
        <div class="col-xs-12 col-sm-10">
            <input type="radio" name="server" value="1" id="server1"><label for="server1">2 x Xeon L5520 (4 ядра, 8 потоков, 2.48ГГц)</label><Br>
        </div>
        <div class="col-sm-2 hidden-xs cpu-chart">
            <div class="" style="width: 51%" title="Наглядное сравнение производительности серверов"></div>
        </div>
        <div class="col-xs-12 col-sm-10">
            <input type="radio" name="server" value="2" id="server2"><label for="server2">2 x Xeon E5-2620 v2 (6 ядра, 12 потоков, 2.6ГГц)</label>
        </div>
        <div class="col-sm-2 hidden-xs cpu-chart">
            <div class="" style="width: 100%" title="Наглядное сравнение производительности серверов"></div>
        </div>
    </div>

    <div class="row border">
        <div class="col-xs-12">
            Выберите количество потоков процессора
        </div>
        <div class="col-xs-8 col-sm-10">
            <div id="cpu"></div>
        </div>
        <div class="col-xs-4 col-sm-2">
            <div id="cpu-text">Потоков: 1</div>
        </div>
    </div>

    <div class="row border">
        <div class="col-xs-12">
            Выберите количество памяти
        </div>
        <div class="col-xs-8 col-sm-10">
            <div id="ram"></div>
        </div>
        <div class="col-xs-4 col-sm-2">
            <div id="ram-text">2048 МБ</div>
        </div>
    </div>

    <div class="row border">
        <div class="col-xs-12">
            Выберите дисковую подсистему
        </div>
        <div class="col-xs-12 col-sm-3">
            <input type="checkbox" name="ssd" value="1" id="ssd"> <label for="ssd">SSD</label><br>
        </div>
        <div class="col-xs-8 col-sm-7">
            <div class="" id="ssd-space"></div>
        </div>
        <div class="col-xs-4 col-sm-2">
            <div class="" id="ssd-space-text">4 ГБ</div>
        </div>
        <div class="col-xs-12 col-sm-3">
            <input type="checkbox" name="hdd" value="1" id="hdd"> <label for="hdd">HDD (SAS)</label>
        </div>
        <div class="col-xs-8 col-sm-7">
            <div class="" id="hdd-space"></div>
        </div>
        <div class="col-xs-4 col-sm-2">
            <div class="" id="hdd-space-text">40 ГБ</div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-2">Итого:</div>
        <div class="col-xs-10" id="summary"></div>
    </div>
</div>