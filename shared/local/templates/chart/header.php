<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Page\Asset;

LongByte\Babel::includeBabel(LongByte\Babel::BABEL_SERVER_CLIENT);
?>

<html>
    <head>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        <meta charset="utf8">
        <meta property="og:title" content="Сравнительные тесты производительности компьютеров."/>
        <meta property="og:description" content="Любительсткое сравнение разных компьютеров, встретившихся в жизни, в различных бенчмарках."/>
        <meta property="og:image" content="http://chart.longbyte.ru<?= SITE_TEMPLATE_PATH ?>/images/chart_soc_logo.png"/>
        <meta property="og:image:width" content="150"/>
        <meta property="og:image:height" content="150"/>
        <meta property="og:url" content= "http://chart.longbyte.ru/" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="yandex-verification" content="f18101f165c3a53e" />
        <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="apple-touch-icon" sizes="144x144" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="theme-color" content="#ffffff">
        <?
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/dialogs.css');
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/js/spoiler/spoiler.css');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-1.11.1.min.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/dialogs.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/jquery.mousewheel.min.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/spoiler/spoiler.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/chart.js');
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.min.css');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/script.js');
        $APPLICATION->ShowHead();
        ?>
    </head>
    <body>
        <? $APPLICATION->ShowPanel(); ?>
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
            "TMP_FILE_MASK" => "tmp_%s.less", // Маска файла для записи временого файла. (%s обязателен, он заменится на таймштамп файла)
            "TARGET_FILE_MASK" => "styles_%s.less.css" // Маска файла для записи css файла. (%s обязателен, он заменится на таймштамп файла)
            ), false, array(
            "HIDE_ICONS" => "Y"
            )
        );
        ?>
        <div class="content">