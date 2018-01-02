<html>
    <head>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <?
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/css/bootstrap.css');
        $APPLICATION->ShowHead();
        ?>
    </head>
    <body>
        <? $APPLICATION->ShowPanel(); ?>
        <div class="container">