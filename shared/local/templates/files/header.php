<?
include_once($_SERVER['DOCUMENT_ROOT'] . '/Mobile_Detect.php');

$device = new Mobile_Detect();
global $isMobile;
$isMobile = $device->isMobile() || $device->isTablet();
?>
<!DOCTYPE html>
<html>
    <head>
        <title><? $APPLICATION->ShowTitle(); ?></title>
        <meta charset="utf8">
        <meta property="og:title" content="Файлопомойка."/>
        <meta property="og:description" content=""/>
        <meta name="yandex-verification" content="376f95df7beb4d9d" />
        <meta property="og:image" content="http://files.longbyte.ru/dir.png"/>
        <meta property="og:image:width" content="150"/>
        <meta property="og:image:height" content="150"/>
        <meta property="og:url" content= "http://files.longbyte.ru/" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
        <link rel="icon" href="/favicon.ico" type="image/x-icon">
        <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="manifest" href="/manifest.json">
        <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
        <meta name="theme-color" content="#ffffff">
        <?
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/jquery-1.11.1.min.js');
        $APPLICATION->SetAdditionalCSS(SITE_TEMPLATE_PATH . '/js/prettyPhoto/css/prettyPhoto.css');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/prettyPhoto/js/jquery.prettyPhoto.js');
        $APPLICATION->AddHeadScript(SITE_TEMPLATE_PATH . '/js/script.js');
        $APPLICATION->ShowHead();
        ?>
    </head>
    <body>
        <? $APPLICATION->ShowPanel(); ?>
        <h1><? $APPLICATION->ShowTitle(); ?></h1>