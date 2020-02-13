<?php

namespace LongByte;

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\IO;

/**
 * Class \LongByte\Webp
 *
 */
class Webp {

    /**
     * 
     * @global \CMain $APPLICATION
     * @param string $content
     * @return mixed
     */
    public static function convertAllToWebp(&$content) {

        global $APPLICATION;

        $obServer = Context::getCurrent()->getServer();

        $arGDInfo = gd_info();

        if (
            !$arGDInfo['WebP Support'] ||
            strpos($obServer->get('HTTP_ACCEPT'), 'image/webp') === false ||
            !function_exists('imagewebp') ||
            strpos($APPLICATION->GetCurDir(), '/bitrix/') !== false
        )
            return;

        $strPattern = '/<img[^>]+src="([^"]+\.(jpg|jpeg|png))"[^>]+>/';
        preg_match_all($strPattern, $content, $arMatches);

        foreach ($arMatches[1] as $strPath) {
            $strWebpPath = self::convertToWebp($strPath);
            $content = str_replace($strPath, $strWebpPath, $content);
        }
    }

    /**
     * 
     * @param string $src
     * @return string
     */
    public static function convertToWebp($src) {
        $newImgPath = $src;

        if (function_exists('imagewebp')) {
            $src = ToLower($src);
            if (IO\File::isFileExists(Application::getDocumentRoot() . $src)) {
                if (strpos($src, '.png')) {
                    $newImg = imagecreatefrompng(Application::getDocumentRoot() . $src);
                    $newImgPath = str_replace('.png', '.webp', $src);
                } elseif (strpos($src, '.jpg') !== false || strpos($src, '.jpeg') !== false) {
                    $newImg = imagecreatefromjpeg(Application::getDocumentRoot() . $src);
                    $newImgPath = str_replace(array('.jpg', '.jpeg'), '.webp', $src);
                }
                if ($newImg) {
                    if (!IO\File::isFileExists(Application::getDocumentRoot() . $newImgPath)) {
                        imagewebp($newImg, Application::getDocumentRoot() . $newImgPath, 90);
                    }
                    imagedestroy($newImg);
                }
            }
        }

        return $newImgPath;
    }

}
