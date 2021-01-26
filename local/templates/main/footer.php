</div>
<?
$APPLICATION->IncludeComponent(
    "longbyte:longbyte.csscompiler", "", array(
    "PATH_TO_FILES" => SITE_TEMPLATE_PATH . "/", // Путь к папке с файлами, которые нужно компилировать
    "FILES" => array(// Список файлов для компиляции, которые будут подключаться в начале
        0 => "global.less",
        1 => "template_styles.less",
    ),
    'FILES_MASK' => array(// Список имен ФАЙЛОВ для компиляции, которые будут подключаться в том числе рекурсивно
    ),
    "PATH_CSS" => SITE_TEMPLATE_PATH . "/css/", // Путь к папке, куда складывать скомпилированный css
    "COMPILER" => "Less", // SASS/Less
    "USE_SETADDITIONALCSS" => "Y", // Подключать скомпилированный css файл через Asset::getInstance()->addCss()?
    "REMOVE_OLD_CSS_FILES" => "Y", // Удалять старые скомпилированные css файлы?
    "TMP_FILE_MASK" => "tmp.less", // Маска файла для записи временого файла. (%s обязателен, он заменится на таймштамп файла)
    "TARGET_FILE_MASK" => "styles.less.css" // Маска файла для записи css файла. (%s обязателен, он заменится на таймштамп файла)
    ), false, array(
    "HIDE_ICONS" => "Y"
    )
);
?>
</body>
</html>