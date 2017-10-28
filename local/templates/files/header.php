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
        <? /* <meta property="og:image" content="http://chart.longbyte.ru<?= SITE_TEMPLATE_PATH ?>/images/chart_soc_logo.png"/>
          <meta property="og:image:width" content="150"/>
          <meta property="og:image:height" content="150"/> */ ?>
        <meta property="og:url" content= "http://files.longbyte.ru/" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
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