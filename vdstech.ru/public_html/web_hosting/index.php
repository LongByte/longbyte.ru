<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "VDSTech - Web-Хостинг. Аренда виртуального сервера под хостинг. Размешение сайта на shared-хостинге.");
$APPLICATION->SetTitle("Web-Хостинг");
$APPLICATION->SetPageProperty("body_style", "background-image: url(/local/templates/vdstech/images/webserver.jpg);");
?>

<a href="/" class="sprite back"></a>
<div class="row service">
    <div class="col-xs-12">
        <h1>Web-Хостинг</h1>
        <p>
            Вашему вниманию предлагается услуга WEB-хостинга. Хостинг – это пространство для Вашего сайта в сети Интернет. От того, какой хостинг вы выберете – зависит скорость 
            загрузки вашего Интернет-ресурса. Как только вы разместили свой сайт на сервере — кто угодно может получить доступ к нему, набрав доменное имя в строке браузера. Доступ 
            к сайту возможен 24 часа в сутки, 7 дней в неделю, 365 дней в год.
        </p>
        <p>
            Обратившись к нам, вы получите:
        </p>
    </div>
    <div class="agvantages clearfix">
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite document"></div></td>
                    <td>
                        Индивидуальные тарифы.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite speed"></div></td>
                    <td>
                        Высокая скорость работы Вашего сайта.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite settings"></div></td>
                    <td>
                        Помощь в первоначальной настройке сервисов.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite support"></div></td>
                    <td>
                        Техническое сопровождение Вашего ресурса.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite day30"></div></td>
                    <td>
                        Бесплатное тестирование в течение 30* дней.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite backup"></div></td>
                    <td>
                        Еженедельное** резервное копирование сайта.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite sale"></div></td>
                    <td>
                        Гибкая система скидок.
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-xs-12">
        <p>
            *предоставляется возможность протестировать работу всех сервисов компании на согласованном тарифном плане в течении 30 календарных дней.
        </p>
        <p>
            ** Общая глубина резервного копирования равна 1 (одному) календарному году. Глубина еженедельного копирования – один месяц. Подробности у менеджера. 
        </p>
    </div>
    <div class="col-xs-12">
        <h2>Пять шагов к хостингу:</h2>
    </div>
    <div class="col-xs-12 flex how">
        <div class="step"><div class="sprite request"></div>Обращение</div>
        <div class="arrow hidden-xs"><div class="sprite arrow"></div></div>
        <div class="step"><div class="sprite document"></div>Заключение<br> договора</div>
        <div class="arrow hidden-xs"><div class="sprite arrow"></div></div>
        <div class="step"><div class="sprite day3"></div>Ожидание<br> 3 дня</div>
        <div class="arrow hidden-xs"><div class="sprite arrow"></div></div>
        <div class="step"><div class="sprite server"></div>Готовый<br> сервер</div>
        <div class="arrow hidden-xs"><div class="sprite arrow"></div></div>
        <div class="step"><div class="sprite settings"></div>Настройка ПО<br> и оборудования</div>
        <div class="arrow hidden-xs"><div class="sprite arrow"></div></div>
        <div class="step"><div class="sprite done"></div>Все готово</div>
    </div>
    <div class="col-xs-12">
        <p>
            Как будет выглядеть услуга на практике:
        </p>
        <p>
            После обращения к нам за получением услуги, мы заключаем с Вами договор на оказание услуг. Затем, в течении трех суток мы организуем для сервер терминалов (удаленных рабочих столов). Вам будут предоставлены реквизиты для доступа для каждого сотрудника Вашей компании. В случае необходимости мы сможем помочь Вам подобрать, поставить, осуществить установку и настройку необходимого Вам для работы программного обеспечения и оборудования.
        </p>
    </div>
</div>
<?
$APPLICATION->IncludeComponent("longbyte:iblock.element.add.form.ajax", "form-order", Array(
    "COMPONENT_TEMPLATE" => "auto-validate-example",
    "STATUS_NEW" => "N", // Деактивировать элемент
    "EVENT_NAME" => "FORM_ORDER", // Почтовое событие
    "USE_CAPTCHA" => "Y", // Использовать CAPTCHA
    "USER_MESSAGE_EDIT" => "", // Сообщение об успешном сохранении
    "USER_MESSAGE_ADD" => "Спасибо за заявку. Мы свяжемся с Вами в ближайшее время.", // Сообщение об успешном добавлении
    "DEFAULT_INPUT_SIZE" => "30", // Размер полей ввода
    "RESIZE_IMAGES" => "N", // Использовать настройки инфоблока для обработки изображений
    "IBLOCK_TYPE" => "vdstech", // Тип инфоблока
    "IBLOCK_ID" => "7", // Инфоблок
    "PROPERTY_CODES" => array(// Свойства, выводимые на редактирование
        0 => "45",
        1 => "47",
        2 => "49",
        3 => "51",
        4 => "NAME",
        5 => "PREVIEW_TEXT",
        6 => "DETAIL_TEXT",
    ),
    "PROPERTY_CODES_REQUIRED" => array(// Свойства, обязательные для заполнения
        0 => "47",
        1 => "51",
        2 => "NAME",
    ),
    "PROPERTY_FILES_ATTACH" => "", // Прикреплять файлы из свойств
    "GROUPS" => array(// Группы пользователей, имеющие право на добавление/редактирование
        0 => "2",
    ),
    "STATUS" => "ANY", // Редактирование возможно
    "ELEMENT_ASSOC" => "CREATED_BY", // Привязка к пользователю
    "MAX_USER_ENTRIES" => "100000", // Ограничить кол-во элементов для одного пользователя
    "MAX_LEVELS" => "100000", // Ограничить кол-во рубрик, в которые можно добавлять элемент
    "LEVEL_LAST" => "Y", // Разрешить добавление только на последний уровень рубрикатора
    "MAX_FILE_SIZE" => "0", // Максимальный размер загружаемых файлов, байт (0 - не ограничивать)
    "PREVIEW_TEXT_USE_HTML_EDITOR" => "N", // Использовать визуальный редактор для редактирования текста анонса
    "DETAIL_TEXT_USE_HTML_EDITOR" => "N", // Использовать визуальный редактор для редактирования подробного текста
    "SEF_MODE" => "N", // Включить поддержку ЧПУ
    ), false
);
?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>