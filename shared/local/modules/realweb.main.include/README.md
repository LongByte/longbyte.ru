# Модуль включаемых областей в БД
****

# Параметры компонента realweb.main.include:

```php
                    $APPLICATION->IncludeComponent("realweb:main.include", ".default", Array(
                        "CODE" => "SOME_TEXT", // Символьный код
                        "EDIT_TEMPLATE" => "", // Шаблон области по умолчанию
                        "ADD_BUTTON" => "Добавит текст", //Надпись на кнопке добавления
                        "EDIT_BUTTON" => "Изменить текст", //Надпись на кнопке изменения
                        "DELETE_BUTTON" => "Удалить текст", //Надпись на кнопке удаления
                        "FORCE_INCLUDE" => "Y", //подключать шаблон компонента, даже если запись не найдена.
                            ), false, array('HIDE_ICONS' => 'N')
                    );
```
