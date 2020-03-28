<?php

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Iblock\IblockTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Conversion\Internals\MobileDetect;

Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Site {

    public static $IS_PRINT;
    public static $isMobile;

    /**
     * 
     * @return bool
     */
    public static function isDevelop() {
        $APPLICATION_ENV = getenv('APPLICATION_ENV');
        $obServer = Context::getCurrent()->getServer();
        return $APPLICATION_ENV === 'develop' || preg_match('/\.local$/', $obServer->getServerName());
    }

    /**
     * 
     * @return bool
     */
    public static function isPrint() {
        if (is_null(static::$IS_PRINT)) {
            $IS_PRINT = false;
            $request = Context::getCurrent()->getRequest();
            $PRINT = $request->get("PRINT");
            if ($PRINT == 'Y') {
                $IS_PRINT = true;
            }
            static::$IS_PRINT = $IS_PRINT;
        }

        return static::$IS_PRINT;
    }

    /**
     * 
     * @return bool
     */
    public static function isMobile() {
        if (is_null(self::$isMobile)) {
            if (Loader::includeModule('conversion')) {
                $device = new MobileDetect();
                self::$isMobile = $device->isMobile() || $device->isTablet();
            }
        }

        return self::$isMobile;
    }

    /**
     * 
     * @return bool
     */
    public static function isIE() {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'rv:11.0') !== false;
    }

    /**
     * 
     */
    public static function Definders() {

        if (Loader::includeModule('iblock')) {

            $result = IblockTable::getList(array(
                    'select' => array('ID', 'IBLOCK_TYPE_ID', 'CODE'),
            ));
            while ($row = $result->fetch()) {
                $row['CODE'] = str_replace('-', '_', $row['CODE']);
                $CONSTANT = ToUpper(implode('_', array('IBLOCK', $row['IBLOCK_TYPE_ID'], $row['CODE'])));
                if (!defined($CONSTANT)) {
                    define($CONSTANT, $row['ID']);
                }
            }
        }

        if (Loader::includeModule('form')) {

            $result = CForm::GetList($by, $order, [], $is_filtered);

            while ($row = $result->fetch()) {
                $CONSTANT = ToUpper(implode('_', array('FORM', $row['SID'])));
                if (!defined($CONSTANT)) {
                    define($CONSTANT, $row['ID']);
                }
            }
        }

        if (Loader::includeModule('iblock') && Loader::includeModule('highloadblock')) {

            $result = HighloadBlockTable::getList(array(
                    'select' => array('ID', 'NAME'),
            ));
            while ($row = $result->fetch()) {
                $row['NAME'] = str_replace('-', '_', $row['NAME']);
                $CONSTANT = ToUpper(implode('_', array('HLBLOCK', $row['NAME'])));
                if (!defined($CONSTANT)) {
                    define($CONSTANT, $row['ID']);
                }
            }
        }
    }

    /**
     * 
     * @param float $fPrice
     * @return string
     */
    public static function FormatPrice($fPrice) {
        return number_format($fPrice, 0, '&nbsp;', '&nbsp;');
    }

    /**
     * 
     * @param string $string
     * @return string
     */
    public static function Translit($string) {
        $params = array("replace_space" => "-", "replace_other" => "-");
        $result = CUtilEx::translit($string, "ru", $params);
        return $result;
    }

    /**
     * 
     */
    public static function onPageStart() {
        self::Definders();
    }

    /**
     * 
     */
    public static function onEpilog() {
        if (class_exists('\Api\Core\Main\Seo')) {
            \Api\Core\Main\Seo::getInstance()->setMetaPage();
        }
    }

    /**
     * Упрощенная обертка ресайза
     * @param int|array $picture
     * @param array[int, int] $arSize
     * @param int $method
     * @param bool $updateVar
     * @return array
     */
    public static function resizeImageGet(&$picture, $arSize, $method = BX_RESIZE_IMAGE_PROPORTIONAL, $updateVar = true) {

        $arReturn = array();

        $arResize = \CFile::ResizeImageGet(is_array($picture) ? $picture['ID'] : $picture, array('width' => $arSize[0], 'height' => $arSize[1]), $method, true);
        if ($arResize) {
            $arReturn = array(
                'WIDTH' => $arResize['width'],
                'HEIGHT' => $arResize['height'],
                'SRC' => $arResize['src'],
            );

            if ($updateVar) {
                if (is_array($picture)) {
                    $picture = array_merge($picture, $arReturn);
                } else {
                    $picture = $arReturn;
                }
            }
        }

        return $arReturn;
    }

}
