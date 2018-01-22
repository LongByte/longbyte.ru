</div>
</main>
<footer>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-4">
                <?
                $APPLICATION->IncludeComponent("bitrix:menu", "top", Array(
                    "COMPONENT_TEMPLATE" => ".default",
                    "ROOT_MENU_TYPE" => "top", // Тип меню для первого уровня
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
</body>
</html>