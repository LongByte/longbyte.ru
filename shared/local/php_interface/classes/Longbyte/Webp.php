<?php

namespace LongByte;

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\IO;
use Bitrix\Main\Config\Option;

/**
 * Class \LongByte\Webp
 *
 */
class Webp {
    /*
     * @var $obSourceFile IO\File
     */

    private $obSourceFile = null;

    /*
     * @var $obTargetFile IO\File
     */
    private $obTargetFile = null;

    /**
     * Проверка поддержки.
     * @global \CMain $APPLICATION
     * @return bool
     */
    public static function checkSupport() {

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
     * Массовая конвертация всех картинок на странице.
     * @param string $content
     */
    public static function convertAllToWebp(&$content) {

        if (self::checkSupport()) {
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
                    $obWebp = new self($strPath);
                    $strWebpPath = $obWebp->getWebpPath();
                    $content = str_replace($strPath, $strWebpPath, $content);
                }
            }
        }
    }

    /**
     * 
     * @param string $strSrc
     */
    public function __construct(string $strSrc) {
        $strUploadDir = Option::get('main', 'upload_dir', 'upload');
        $strToUploadDir = '';
        if (strpos($strSrc, '/' . $strUploadDir) !== 0) {
            $strToUploadDir = '/' . $strUploadDir . '/webp';
        }
        $this->obSourceFile = new IO\File(Application::getDocumentRoot() . $strSrc);
        $this->obTargetFile = new IO\File(Application::getDocumentRoot() . $strToUploadDir . preg_replace('/\.[^\.]+$/i', '.webp', $strSrc));
    }

    /**
     * Конвертация форматов.
     * @return string
     */
    public function getWebpPath() {
        $obImage = null;
        if (!self::checkSupport() || !$this->getSourceFile()->isExists()) {
            return $this->getSourceSrc();
        }

        if ($this->getTargetFile()->isExists() && !$this->isNeedUpdate()) {
            return $this->checkCorrectImage();
        }

        if ($this->isPng()) {
            $obImage = imagecreatefrompng($this->getSourceFile()->getPath());
        } elseif ($this->isBmp()) {
            $obImage = imagecreatefrombmp($this->getSourceFile()->getPath());
        } elseif ($this->isJpg()) {
            $obImage = imagecreatefromjpeg($this->getSourceFile()->getPath());
        }
        if (!is_null($obImage)) {
            $this->getTargetFile()->putContents(' ');   //создаем путь до файла и файл 
            imagewebp($obImage, $this->getTargetFile()->getPath(), 90);
            imagedestroy($obImage);
        }

        return $this->checkCorrectImage();
    }

    /**
     * 
     * @return IO\File
     */
    private function getSourceFile() {
        return $this->obSourceFile;
    }

    /**
     * 
     * @return IO\File
     */
    private function getTargetFile() {
        return $this->obTargetFile;
    }

    /**
     * 
     * @return string
     */
    private function getSourceSrc() {
        return str_replace(Application::getDocumentRoot(), '', $this->getSourceFile()->getPath());
    }

    /**
     * 
     * @return string
     */
    private function getTargetSrc() {
        return str_replace(Application::getDocumentRoot(), '', $this->getTargetFile()->getPath());
    }

    /**
     * Проверка, что файл существует и имеет размер больше 0 (сконвертировался корректно)
     * @return string
     */
    private function checkCorrectImage() {
        if ($this->getTargetFile()->isExists() && $this->getTargetFile()->getSize() > 0) {
            return $this->getTargetSrc();
        }
        return $this->getSourceSrc();
    }

    /**
     * Проверка даты обновления файла
     * @return bool
     */
    private function isNeedUpdate() {
        return $this->getTargetFile()->getModificationTime() < $this->getSourceFile()->getModificationTime();
    }

    /**
     * 
     * @return bool
     */
    private function isPng() {
        return $this->getSourceFile()->getExtension() == 'png' && $this->getSourceFile()->getContentType() == 'image/png';
    }

    /**
     * 
     * @return bool
     */
    private function isBmp() {
        return $this->getSourceFile()->getExtension() == 'bmp' && $this->getSourceFile()->getContentType() == 'image/bmp';
    }

    /**
     * 
     * @return bool
     */
    private function isJpg() {
        return in_array($this->getSourceFile()->getExtension(), array('jpg', 'jpeg')) && $this->getSourceFile()->getContentType() == 'image/jpeg';
    }

}
