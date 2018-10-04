<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "VDSTech - Облачное рабочее место руководителя. Аренда удаленного рабочего места.");
$APPLICATION->SetTitle("Облачное рабочее место руководителя");
$APPLICATION->SetPageProperty("body_style", "background-image: url(/local/templates/vdstech/images/cloud.jpg);");
?>

<a href="/" class="sprite back"></a>
<div class="row service">
    <div class="col-xs-12">
        <h1>Облачное рабочее место руководителя</h1>
        <p>
            Вы часто путешествуете? Бизнес требует присутствия на рабочем месте даже если вы далеко от офиса? Где бы вы не находились – на отдыхе, в самолете, в аэропорту, или в автомобиле – вы всегда сможете включится в работу в течении считанных минут с любого* устройства!
        </p>
        <p>
            Наша компания предлагает Вам услугу Облачное рабочее место руководителя. Услуга представляет из себя облачный сервис доступный с любой точки планеты где есть доступ к сети Интернет. При помощи RDP-клиента вы сможете подключится к облачной операционной системе, и получить доступ к тем сервисам, которые вы используете в своей повседневной деятельности.
        </p>
    </div>
    <div class="agvantages clearfix">
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite computer"></div></td>
                    <td>
                        Мощный ПК для Ваших потребностей
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite world"></div></td>
                    <td>
                        Удаленный доступ с любых* устройств
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite windows"></div></td>
                    <td>
                        Привычная Вам операционная система
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite setup"></div></td>
                    <td>
                        Возможность установки любых приложений
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite hdd"></div></td>
                    <td>
                        Доступность локальных устройств в облаке
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-xs-12 col-sm-6 col-md-4">
            <table>
                <tr>
                    <td><div class="sprite support"></div></td>
                    <td>
                        Грамотная техническая поддержка
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="col-xs-12">
        <h2>Пять шагов к облачному рабочему месту руководителя:</h2>
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