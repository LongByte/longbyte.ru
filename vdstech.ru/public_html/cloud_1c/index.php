<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "VDSTech - Облачная 1С-Бухгалтерия. Аренда сервера под 1С");
$APPLICATION->SetPageProperty("body_style", "background-image: url(/local/templates/vdstech/images/1c-cloud.jpg);");
$APPLICATION->SetTitle("Облачная 1С-Бухгалтерия");
?>

<a href="/" class="sprite back"></a>
<div class="row service">
    <div class="col-xs-12">
        <h1>Облачная 1С-Бухгалтерия</h1>
        <p>
            Часто наши клиенты обращаются к нам с просьбой оказать помощь в случае, когда их база данных 1С перестала корректно функционировать, а резервной копии сделано не было. Такая ситуация может возникать по различным причинам, таким как вирусная активность, некорректное обновление бухгалтерского программного обеспечения и т.д.
        </p>
        <p>
            Мы предлагаем решить эту проблему путем размещения Вашей базы данных в нашем дата-центре.
        </p>
    </div>
    <div class="agvantages clearfix">
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite backup"></div></td>
                    <td>
                        Ежедневное резервное копирование Вашей базы данных на нашем, защищенном от возможной вирусной активности кластере серверов.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite restore"></div></td>
                    <td>
                        Возможностью оперативного восстановления данных из резервной копии.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite update"></div></td>
                    <td>
                        Периодические бесплатные обновления платформы 1С.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite fulltime"></div></td>
                    <td>
                        Доступность сервисов 1С-Бухгалтерии для Вашей компании 365 дней в году 24 часа 7 дней в неделю с любой точки земного шара где есть доступ к сети Интернет.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite secure"></div></td>
                    <td>
                        Защищенный канал связи с нашим дата-центром, для обеспечения защиты Ваших финансовых данных.
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite mobile"></div></td>
                    <td>
                        Возможность получения защищенного доступа к 1C-Бухгалтерии с помощью устройств на базе Android и IOS.
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-xs-12">
        <h2>Пять шагов к облачной 1С:</h2>
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
            После обращения к нам за получением услуги, мы заключаем с Вами договор на оказание услуг. Затем, в течении трех суток мы организуем для Вас сервер терминалов (удаленных рабочих столов). Вам будут предоставлены реквизиты для доступа для каждого сотрудника Вашей компании. В случае необходимости мы сможем помочь Вам подобрать, поставить, осуществить установку и настройку необходимого Вам для работы программного обеспечения и оборудования.
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