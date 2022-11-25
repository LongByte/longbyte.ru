<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = array(
    "NAME" => GetMessage("IBLOCK_ELEMENT_ADD_FORM_NAME_AJAX"),
    "DESCRIPTION" => GetMessage("IBLOCK_ELEMENT_ADD_FORM_DESCRIPTION_AJAX"),
    "ICON" => "/images/eaddform.gif",
    "PATH" => array(
        "ID" => "content",
        "CHILD" => array(
            "ID" => "iblock_element_add_ajax",
            "NAME" => GetMessage("T_IBLOCK_DESC_ELEMENT_ADD_AJAX"),
        ),
    ),
);

/* Version 1.5
 * 1.0: От обычного компонента отличает тем, что работает через AJAX
 * 1.1: Добавлена возможность сразу вызывать почтовые события. В письмо передаются все ключи элемента. Свосвтва виду #PROPERTY_CODE#
 * 1.2: Исправлена проблема с добавлением в письмо множественного значения списка. В шаблоне ski-service пример униврсальных селекторов.
 * 1.3: В почтовое событие прокидываются поля пользователя, от которого создается элемент. #USER_поле#. 
 * Шаблон с примерами переименован в auto-validate-example. Добавлена возможность обработки E свойств (привязка к элементам).
 * Добавлено добавление файлов AJAX
 * 1.4: Добавлена возможность прикреплять файлы из добавленного элемента.
 * 1.5: Добавлена совместимость AJAX с кодировкой сайта, отличной от UTF-8
 * 
 * Know issue:
 * Добавить обработку G полей (привязка к разделам)
 * Добавить возможность обновлять капчу
 */
?>