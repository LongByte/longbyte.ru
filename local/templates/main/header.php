<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Page\Asset;

LongByte\Babel::includeBabel(LongByte\Babel::BABEL_SERVER_CLIENT);
LongByte\Vue::includeVueJS();
?>
<!DOCTYPE html>
<html>
    <head>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        <meta charset="utf8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <?
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/bootstrap-grid.css');
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/css/bootstrap.css');

        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-1.11.1.min.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.js');
        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/js/jquery-ui/jquery-ui.min.css');

        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/dialogs.css');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/dialogs.js');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/dialogs-2.0/jquery.mousewheel.min.js');

        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/js/prettyPhoto/css/prettyPhoto.css');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/prettyPhoto/js/jquery.prettyPhoto.js');

        Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . '/js/spoiler/spoiler.css');
        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/spoiler/spoiler.js');

        Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/script.js');
        $APPLICATION->ShowHead();
        ?>
    </head>
    <body>
        <? $APPLICATION->ShowPanel(); ?>
        <div class="content">