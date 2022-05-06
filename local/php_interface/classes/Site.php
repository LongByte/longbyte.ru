<?php

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Iblock\IblockTable;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Conversion\Internals\MobileDetect;

Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Site
{

    public static ?bool $IS_PRINT = null;
    public static ?bool $isMobile = null;
    public static ?bool $isMainPage = null;

    public static function isDevelop(): bool
    {
        $APPLICATION_ENV = getenv('APPLICATION_ENV');
        $obServer = Context::getCurrent()->getServer();
        return $APPLICATION_ENV === 'develop' || preg_match('/\.local$/', $obServer->getServerName());
    }

    public static function isPrint(): bool
    {
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

    public static function isMobile(): bool
    {
        if (is_null(self::$isMobile)) {
            if (Loader::includeModule('conversion')) {
                $device = new MobileDetect();
                self::$isMobile = $device->isMobile() || $device->isTablet();
            }
        }

        return self::$isMobile;
    }

    public static function isIE(): bool
    {
        return strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'rv:11.0') !== false;
    }

    public static function isEdge(): bool
    {
        return preg_match('/Edge/i', $_SERVER['HTTP_USER_AGENT']);
    }

    public static function isMainPage(): bool
    {
        if (is_null(self::$isMainPage)) {
            global $APPLICATION;
            self::$isMainPage = $APPLICATION->GetCurPage() == '/';
        }
        return self::$isMainPage;
    }

    public static function Definders(): void
    {

        if (Loader::includeModule('iblock')) {

            $result = IblockTable::getList(array(
                'select' => array('ID', 'IBLOCK_TYPE_ID', 'CODE'),
            ));
            while ($row = $result->fetch()) {
                $row['CODE'] = str_replace('-', '_', $row['CODE']);
                $CONSTANT = ToUpper(implode('_', array('IBLOCK', $row['IBLOCK_TYPE_ID'], $row['CODE'])));
                if (!defined($CONSTANT)) {
                    define($CONSTANT, (int) $row['ID']);
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
     * @param float|int $fPrice
     * @return string
     */
    public static function FormatPrice($fPrice): string
    {
        return number_format($fPrice, 0, '&nbsp;', '&nbsp;');
    }

    public static function Translit(string $string): string
    {
        $params = array("replace_space" => "-", "replace_other" => "-");
        $result = CUtilEx::translit($string, "ru", $params);
        return $result;
    }

    public static function onPageStart(): void
    {
        self::Definders();
    }

    public static function onEpilog(): void
    {
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
    public static function resizeImageGet(&$picture, $arSize, $method = BX_RESIZE_IMAGE_PROPORTIONAL, $updateVar = true)
    {

        $arReturn = array();

        if (!is_array($picture) && !is_numeric($picture)) {
            $destSrc = '/' . Option::get('main', 'upload_dir', 'upload') . '/tmp' . $picture;
            $destSrcFull = Application::getDocumentRoot() . $destSrc;
            $bResize = \CFile::ResizeImageFile(Application::getDocumentRoot() . $picture, $destSrcFull, array('width' => $arSize[0], 'height' => $arSize[1]), $method);
            if ($bResize) {
                if ($updateVar) {
                    $picture = $destSrc;
                }

                $arReturn = $destSrc;
            } else {
                $arReturn = false;
            }
        } else {

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
        }

        return $arReturn;
    }

}
