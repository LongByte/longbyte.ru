<!DOCTYPE html>
<html>
    <head>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="theme-color" content="#ffffff">
        <meta property="og:title" content="<? $APPLICATION->ShowTitle(); ?>"/>
        <meta property="og:description" content="<? $APPLICATION->ShowProperty('description'); ?>"/>
        <meta property="og:image" content="https://vdstech.ru/android-chrome-192x192.png"/>
        <meta property="og:image:width" content="192"/>
        <meta property="og:image:height" content="192"/>
        <meta property="og:url" content= "https://vdstech.ru/" />
        <?
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery-1.12.4.min.js');
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/bootstrap.css');

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/simple-validate/simple-validate.js');
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/simple-validate/simple-validate.css');

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/jquery.mousewheel.min.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/dialogs.js');
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/dialogs.css');

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/select2_40/js/select2.full.min.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/select2_40/js/i18n/ru.js');
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/select2_40/css/select2.min.css');

        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/script.js');
        $APPLICATION->ShowHead();
        ?>
        <meta name="yandex-verification" content="24b0dd305f34da8b" />
        <meta name="interkassa-verification" content="2f5c5c35b213665e15b166aff131e72c" />
    </head>
    <body style="<? $APPLICATION->ShowProperty('body_style') ?>">
        <!-- Yandex.Metrika counter -->
        <script type="text/javascript" >
            (function (d, w, c) {
                (w[c] = w[c] || []).push(function () {
                    try {
                        w.yaCounter47263095 = new Ya.Metrika({
                            id: 47263095,
                            clickmap: true,
                            trackLinks: true,
                            accurateTrackBounce: true,
                            webvisor: true,
                            trackHash: true
                        });
                    } catch (e) {
                    }
                });

                var n = d.getElementsByTagName("script")[0],
                    s = d.createElement("script"),
                    f = function () {
                        n.parentNode.insertBefore(s, n);
                    };
                s.type = "text/javascript";
                s.async = true;
                s.src = "https://mc.yandex.ru/metrika/watch.js";

                if (w.opera == "[object Opera]") {
                    d.addEventListener("DOMContentLoaded", f, false);
                } else {
                    f();
                }
            })(document, window, "yandex_metrika_callbacks");
        </script>
        <noscript><div><img src="https://mc.yandex.ru/watch/47263095" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
        <!-- /Yandex.Metrika counter -->
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=UA-107013758-3"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());

            gtag('config', 'UA-107013758-3');
        </script>
        <? $APPLICATION->ShowPanel(); ?>
        <?
        $APPLICATION->IncludeComponent(
            "realweb:realweb.csscompiler", "", array(
            "PATH_TO_FILES" => SITE_TEMPLATE_PATH . "/", // Путь к папке с файлами, которые нужно компилировать
            "FILES" => array(// Список файлов для компиляции, которые будут подключаться в начале
                0 => "global.less",
                1 => "css/template_styles.less",
                2 => "css/calculate.less",
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
        <?
        $APPLICATION->IncludeComponent(
            "realweb:realweb.spritecompiler", "", array(
            "PATH_TO_FILES" => SITE_TEMPLATE_PATH . "/images/vector/", // Путь к папке с файлами, которые нужно компилировать
            "FILES" => array(// Список файлов для компиляции, которые будут подключаться в начале
            ),
            'FILES_MASK' => array(// Список имен ФАЙЛОВ для компиляции, которые будут подключаться в том числе рекурсивно
                0 => "*", //Доступна *, чтобы брать все файлы
            ),
            'ID_PREFIX' => 'icon-', //префикс для id самих иконок
            "PATH_TO_FILES_SPRITE" => SITE_TEMPLATE_PATH . "/images/svg/", // Путь к папке, куда складывать скомпилированный спрайт
            "REMOVE_OLD_SPRITE_FILES" => "Y", // Удалять старые скомпилированные svg файлы?
            "TARGET_FILE_MASK" => "sprite-%s.compiled.svg" // Маска файла для записи svg файла. (%s обязателен, он заменится на таймштамп файла)
            ), false, array(
            "HIDE_ICONS" => "Y"
            )
        );
        ?>
        <header>
            <div class="container-fluid main">
                <div class="row">
                    <div class="col-sm-2 hidden-xs"></div>
                    <div class="col-xs-12 col-sm-8 text-center">
                        <div class="logo">
                            <a href="/">
                                <img src="/android-chrome-192x192.png" width="80" alt="VDSTech">
                                <? if ($APPLICATION->GetCurPage() == '/') { ?>
                                    <h1>VDSTech<span class="hidden-xs"> - облачные технологии</span></h1>
                                <? } else { ?>
                                    VDSTech<span class="hidden-xs"> - облачные технологии</span>
                                <? } ?>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-2 lk-wrapper">
                        <a href="https://billing.vdstech.ru/" target="_blank" class="button inline-block">Личный кабинет</a>
                    </div>
                </div>
            </div>
        </header>
        <main class="fade-bg shadow">
            <div class="container">