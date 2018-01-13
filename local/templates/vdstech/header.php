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
        $APPLICATION->ShowHead();
        ?>
        <meta name="yandex-verification" content="24b0dd305f34da8b" />
        <meta name="interkassa-verification" content="2f5c5c35b213665e15b166aff131e72c" />
    </head>
    <body>
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
        <header>
            <div class="container-fluid main">
                <div class="row">
                    <div class="col-sm-2 hidden-xs"></div>
                    <div class="col-xs-12 col-sm-8 text-center">
                        <h1>
                            <a href="/">
                                <img src="/android-chrome-192x192.png" width="80">
                                VDSTech - облачные технологии
                            </a>
                        </h1>
                    </div>
                    <div class="col-xs-12 col-sm-2 text-right">
                        <a href="https://billing.vdstech.ru/" target="_blank" class="button inline-block">Личный кабинет</a>
                    </div>
                </div>
            </div>
        </header>