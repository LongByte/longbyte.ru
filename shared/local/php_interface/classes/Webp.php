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
     * @param string $content
     */
    public static function convertAllToWebp(&$content) {

        if (self::_checkSupport()) {
            $arPatterns = array(
                '/<img[^>]*src="([^"]+\.(jpg|jpeg|png|bmp))"[^>]*>/',
                '/<[^>]*style="[^"]*url\(([^)]+\.(jpg|jpeg|png|bmp))\)[^"]*"[^>]*>/',
            );

            foreach ($arPatterns as $strPattern) {
                preg_match_all($strPattern, $content, $arMatches);

                foreach ($arMatches[1] as $strPath) {
                    $strWebpPath = self::convertToWebp($strPath);
                    $content = str_replace($strPath, $strWebpPath, $content);
                }
            }
        }
    }

    /**
     * 
     * @param string $strSrc
     * @return string
     */
    public static function convertToWebp($strSrc) {
        $strNewSrc = $strSrc;

        if (self::_checkSupport()) {
            $strSrc = ToLower($strSrc);
            if (IO\File::isFileExists(Application::getDocumentRoot() . $strSrc)) {
                if (strpos($strSrc, '.png')) {
                    $obImage = imagecreatefrompng(Application::getDocumentRoot() . $strSrc);
                    $strNewSrc = str_replace('.png', '.webp', $strSrc);
                } elseif (strpos($strSrc, '.bmp')) {
                    $obImage = imagecreatefrombmp(Application::getDocumentRoot() . $strSrc);
                    $strNewSrc = str_replace('.bmp', '.webp', $strSrc);
                } elseif (strpos($strSrc, '.jpg') !== false || strpos($strSrc, '.jpeg') !== false) {
                    $obImage = imagecreatefromjpeg(Application::getDocumentRoot() . $strSrc);
                    $strNewSrc = str_replace(array('.jpg', '.jpeg'), '.webp', $strSrc);
                }
                if ($obImage) {
                    if (!IO\File::isFileExists(Application::getDocumentRoot() . $strNewSrc)) {
                        imagewebp($obImage, Application::getDocumentRoot() . $strNewSrc, false);
                    }
                    imagedestroy($obImage);
                }
            }
        }

        return $strNewSrc;
    }

    /**
     * 
     * @global \CMain $APPLICATION
     * @return bool
     */
    private static function _checkSupport() {

        global $APPLICATION;
        $obServer = Context::getCurrent()->getServer();
        $arGDInfo = gd_info();

        return (
            $arGDInfo['WebP Support'] &&
            function_exists('imagewebp') &&
            strpos($obServer->get('HTTP_ACCEPT'), 'image/webp') !== false &&
            strpos($APPLICATION->GetCurDir(), '/bitrix/') === false &&
            $APPLICATION->GetProperty('disable_webp') != 'Y' &&
            intval(phpversion()) >= 7
            );
    }

}
