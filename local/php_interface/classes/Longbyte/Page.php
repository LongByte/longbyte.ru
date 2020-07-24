<?php

namespace Longbyte;

class Page {

    public function onEndBufferContent(&$strContent) {
        self::modificateImages($strContent);
        self::modificateFrames($strContent);
//        self::preloadCss($strContent);
//        self::deleteKernelJs($strContent);
//        self::deleteKernelCss($strContent);
        self::replaceScripts($strContent);
    }

    public function modificateImages(&$strContent) {
        $re = '/<img[^>]+>/m';
        preg_match_all($re, $strContent, $arMatches, PREG_SET_ORDER, 0);
        $strContent = str_replace('<img', '<img loading="lazy"', $strContent);
    }

    public static function replaceScripts(&$content) {
        $content = str_replace('type="text/javascript"', '', $content);
        $content = str_replace("type='text/javascript'", '', $content);
    }

    function deleteKernelJs(&$content) {
        global $USER, $APPLICATION;
        if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false)
            return;
        if ($APPLICATION->GetProperty("save_kernel") == "Y")
            return;

        $arPatternsToRemove = Array(
            '/<script.+?src=".+?kernel_main\/kernel_main\.js\?\d+"><\/script\>/',
            '/<script.+?src=".+?kernel_currency\/kernel_currency\.js\?\d+"><\/script\>/',
            '/<script.+?src=".+?kernel_socialservices\/kernel_socialservices\.js\?\d+"><\/script\>/',
            '/<script.+?src=".+?bitrix\/js\/main\/core\/core[^"]+"><\/script\>/',
            '/<script.+?>BX\.(setCSSList|setJSList)\(\[.+?\]\).*?<\/script>/',
            '/<script.+?>if\(\!window\.BX\)window\.BX.+?<\/sc ript>/',
            '/<script[^>]+?>\(window\.BX\|\|top\.BX\)\.message[^<]+<\/sc ript>/',
        );

        $content = preg_replace($arPatternsToRemove, "", $content);
        $content = preg_replace("/\n{2,}/", "\n\n", $content);
    }

    function deleteKernelCss(&$content) {

        global $USER, $APPLICATION;
        if ((is_object($USER) && $USER->IsAuthorized()) || strpos($APPLICATION->GetCurDir(), "/bitrix/") !== false)
            return;
        if ($APPLICATION->GetProperty("save_kernel") == "Y")
            return;

        $arPatternsToRemove = Array(
            '/<link.+?href=".+?kernel_main\/kernel_main\.css\?\d+"[^>]+>/',
            '/<link.+?href=".+?kernel_socialservices\/kernel_socialservices\.css\?\d+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/js\/main\/core\/css\/core[^"]+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/styles.css[^"]+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/templates\/[\w\d_-]+\/template_styles.css[^"]+"[^>]+>/',
            '/<link.+?href=".+?bitrix\/panel[^"]+"[^>]+>/',
        );

        $content = preg_replace($arPatternsToRemove, "", $content);
        $content = preg_replace("/\n{2,}/", "\n\n", $content);
    }

    public static function modificateFrames(&$strContent) {
        $re = '/<iframe.+src="[^"]+youtube[^"]+"[^>]+>/m';
        preg_match_all($re, $strContent, $arMatches, PREG_SET_ORDER, 0);
        foreach ($arMatches as $arMatch) {
            $strLazyFrame = str_replace('<iframe', '<iframe loading="lazy"', $arMatch[0]);
            $strContent = str_replace($arMatch[0], $strLazyFrame, $strContent);
        }


        $arYoutubeVideos = array();
        $re = '/<iframe.+src="[^"]+youtube[^"]+"[^>]+>/m';
        preg_match_all($re, $strContent, $arMatches, PREG_SET_ORDER, 0);
        foreach ($arMatches as $arMatch) {
            $strVideoFrame = $arMatch[0];
            $strVideoHash = md5($strVideoFrame);
            $arYoutubeVideos[$strVideoHash] = $strVideoFrame;
            $strContent = str_replace($strVideoFrame, '<div data-hash="' . $strVideoHash . '" class="youtube-video-frame js-youtube-video-frame"></div>', $strContent);
        }

        if (count($arMatches) > 0) {
            $strScript = "<script> $(document).ready(function (e) {setTimeout(function () {";
            foreach ($arYoutubeVideos as $strVideoHash => $strVideoFrame) {
                $strScript .= "$('div[data-hash=\"" . $strVideoHash . "\"]').replaceWith('" . $strVideoFrame . "');";
            }
            $strScript .= "}, 2000); });</script>";
        }

        $strContent = str_replace('</head>', $strScript . '</head>', $strContent);
    }

}
