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
     * Массовая конвертация всех картинок.
     * @param string $content
     */
    public static function convertAllToWebp(&$content) {

        if (self::_checkSupport()) {
            $arPatterns = array(
                '/<img[^>]* src="([^"]+\.(jpg|jpeg|png|bmp))"[^>]*>/',
                '/<img[^>]* data-src="([^"]+\.(jpg|jpeg|png|bmp))"[^>]*>/',
                '/<[^>]*style="[^"]*url\(([^)]+\.(jpg|jpeg|png|bmp))\)[^"]*"[^>]*>/',
            );

            foreach ($arPatterns as $strPattern) {
                preg_match_all($strPattern, $content, $arMatches);

                foreach ($arMatches[1] as $keyMatch => $strPath) {
                    if (strpos($arMatches[0][$keyMatch], 'data-webp="skip"') !== false) {
                        continue;
                    }
                    $strWebpPath = self::convertToWebp($strPath);
                    $content = str_replace($strPath, $strWebpPath, $content);
                }
            }
        }
    }

    /**
     * Конвертация форматов.
     * @param string $strSrc
     * @return string
     */
    public static function convertToWebp($strSrc) {
        if (!self::_checkSupport() || !IO\File::isFileExists(Application::getDocumentRoot() . $strSrc)) {
            return $strSrc;
        }

        $strTargetSrc = preg_replace('/\.[^\.]+$/i', '.webp', $strSrc);
        if (IO\File::isFileExists(Application::getDocumentRoot() . $strTargetSrc)) {
            return self::_checkCorrectImage($strSrc, $strTargetSrc);
        }

        if (strpos($strSrc, '.png')) {
            $obImage = imagecreatefrompng(Application::getDocumentRoot() . $strSrc);

            if (self::_isPhp56BetaEnabled()) {
                list($iWidth, $iHeight) = getimagesize(Application::getDocumentRoot() . $strSrc);
                $obJpgImage = imagecreatetruecolor($iWidth, $iHeight);
                $obWhite = imagecolorallocate($obJpgImage, 255, 255, 255);
                imagefilledrectangle($obJpgImage, 0, 0, $iWidth, $iHeight, $obWhite);
                imagecopy($obJpgImage, $obImage, 0, 0, 0, 0, $iWidth, $iHeight);

                $strTempSrc = str_replace('.png', '_tmp.jpg', $strSrc);
                imagejpeg($obJpgImage, Application::getDocumentRoot() . $strTempSrc, 100);
                $obImage = imagecreatefromjpeg(Application::getDocumentRoot() . $strTempSrc);
            }
        } elseif (strpos($strSrc, '.bmp')) {
            $obImage = imagecreatefrombmp(Application::getDocumentRoot() . $strSrc);
        } elseif (strpos($strSrc, '.jpg') !== false || strpos($strSrc, '.jpeg') !== false) {
            $obImage = imagecreatefromjpeg(Application::getDocumentRoot() . $strSrc);
        }
        if ($obImage) {
            imagewebp($obImage, Application::getDocumentRoot() . $strTargetSrc, 90);
            imagedestroy($obImage);
        }

        return self::_checkCorrectImage($strSrc, $strTargetSrc);
    }

    /**
     * Проверка, что файл существует и имеет размер больше 0 (сконвертировался корректно)
     * @param string $strSrc
     * @param string $strTargetSrc
     * @return string
     */
    private static function _checkCorrectImage($strSrc, $strTargetSrc) {
        $obTargetFile = new IO\File(Application::getDocumentRoot() . $strTargetSrc);
        if ($obTargetFile->isExists()) {
            if ($obTargetFile->getSize() > 0) {
                return $strTargetSrc;
            }
        }
        return $strSrc;
    }

    /**
     * Проверка поддержки.
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
            $APPLICATION->GetProperty('enable_webp') != 'N'
            );
    }

    /**
     * Возможность включить конвертацию на php 5.6.
     * На php 5.6 проблема с прозрачностью png. При включении этой опции конвертация идет через jpg с потерей прозрачности фона. Но всегда есть шанс, что что-то пойдет не так.
     * @global \CMain $APPLICATION
     * @return bool
     */
    private static function _isPhp56BetaEnabled() {
        global $APPLICATION;
        return $APPLICATION->GetProperty('enable_webp_php56_beta') == 'Y';
    }

}
