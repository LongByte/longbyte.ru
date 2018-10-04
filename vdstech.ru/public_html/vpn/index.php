<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Шифрованный VPN-канал");
?>

<a href="/" class="sprite back"></a>
<div class="row service">
    <div class="col-xs-12">
        <h1>Шифрованный VPN-канал</h1>
        <p>
            Страница в разработке
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