<html>
    <head>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        <meta charset="utf8">
        <meta property="og:title" content="Сравнительные тесты производительности компьютеров."/>
        <meta property="og:description" content="Любительсткое сравнение разных компьютеров, встретившихся в жизни, в различных бенчмарках."/>
        <meta property="og:image" content="http://chart.longbyte.ru<?=SITE_TEMPLATE_PATH?>/images/chart_soc_logo.png"/>
        <meta property="og:image:width" content="150"/>
        <meta property="og:image:height" content="150"/>
        <meta property="og:url" content= "http://chart.longbyte.ru/" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="yandex-verification" content="f18101f165c3a53e" />
        <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <?
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/dialogs.css');
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/spoiler/spoiler.css');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery-1.11.1.min.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/dialogs.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/jquery.mousewheel.min.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/spoiler/spoiler.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/chart.js');
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.min.css');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/script.js');
        $APPLICATION->ShowHead();
        ?>
    </head>
    <body>
        <? $APPLICATION->ShowPanel(); ?>
        <div class="content">