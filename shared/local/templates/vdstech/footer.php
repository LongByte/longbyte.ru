</div>
</main>
<footer>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-4">
                <?
                $APPLICATION->IncludeComponent("bitrix:menu", "bottom", Array(
                    "COMPONENT_TEMPLATE" => "bottom",
                    "ROOT_MENU_TYPE" => "bottom", // Тип меню для первого уровня
                    "MENU_CACHE_TYPE" => "A", // Тип кеширования
                    "MENU_CACHE_TIME" => "86400", // Время кеширования (сек.)
                    "MENU_CACHE_USE_GROUPS" => "Y", // Учитывать права доступа
                    "MENU_CACHE_GET_VARS" => "", // Значимые переменные запроса
                    "MAX_LEVEL" => "1", // Уровень вложенности меню
                    "CHILD_MENU_TYPE" => "left", // Тип меню для остальных уровней
                    "USE_EXT" => "N", // Подключать файлы с именами вида .тип_меню.menu_ext.php
                    "DELAY" => "N", // Откладывать выполнение шаблона меню
                    "ALLOW_MULTI_SELECT" => "N", // Разрешить несколько активных пунктов одновременно
                    ), false
                );
                ?>
            </div>
            <div class="hidden-xs hidden-sm col-md-4">

            </div>
            <div class="col-xs-12 col-sm-6 col-md-4">
                &copy; ВДС Облачные технологии<br>
                ИНН: 519056878026<br>
                ОГРНИП: 316332800070571<br>
                Телефон <a href="+79268793297">+7 (926) 879 32 97</a><br>
                Отдел продаж: <a href="mailto:sale@vdstech.ru">sale@vdstech.ru</a><br>
                Техническая поддержка: <a href="mailto:support@vdstech.ru">support@vdstech.ru</a>
            </div>
        </div>
    </div>
</footer>
   <!-- BEGIN JIVOSITE CODE {literal} -->
<script type='text/javascript'>
(function(){ var widget_id = 'oHPmrMEWUM';var d=document;var w=window;function l(){
var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = '//code.jivosite.com/script/widget/'+widget_id; var ss = document.getElementsByTagName('script')[0]; ss.parentNode.insertBefore(s, ss);}if(d.readyState=='complete'){l();}else{if(w.attachEvent){w.attachEvent('onload',l);}else{w.addEventListener('load',l,false);}}})();</script>
<!-- {/literal} END JIVOSITE CODE -->
</body>
</html>