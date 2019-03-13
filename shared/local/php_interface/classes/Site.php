<?php

use Bitrix\Main\Localization\Loc;

Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

class Site {

    public static $IS_PRINT;

    public static function IsDevelop() {
        $APPLICATION_ENV = getenv('APPLICATION_ENV');
        if ($APPLICATION_ENV === 'develop') {
            return true;
        }
        return false;
    }

    public static function IsPrint() {
        if (is_null(static::$IS_PRINT)) {
            $IS_PRINT = false;
            $request = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
            $PRINT = $request->get("PRINT");
            if ($PRINT == 'Y') {
                $IS_PRINT = true;
            }
            static::$IS_PRINT = $IS_PRINT;
        }

        return static::$IS_PRINT;
    }

    public static function Definders() {

        \Bitrix\Main\Loader::includeModule('iblock');

        $result = \Bitrix\Iblock\IblockTable::getList(array(
                'select' => array('ID', 'IBLOCK_TYPE_ID', 'CODE'),
        ));
        while ($row = $result->fetch()) {
            $row['CODE'] = str_replace('-', '_', $row['CODE']);
            $CONSTANT = ToUpper(implode('_', array('IBLOCK', $row['IBLOCK_TYPE_ID'], $row['CODE'])));
            if (!defined($CONSTANT)) {
                define($CONSTANT, $row['ID']);
            }
        }

        if (\Bitrix\Main\Loader::includeModule('form')) {

            $result = CForm::GetList($by, $order, [], $is_filtered);

            while ($row = $result->fetch()) {
                $CONSTANT = ToUpper(implode('_', array('FORM', $row['SID'])));
                if (!defined($CONSTANT)) {
                    define($CONSTANT, $row['ID']);
                }
            }
        }
    }

    public static function DeclOfNum($number, $titles) {
        $cases = array(2, 0, 1, 1, 1, 2);
        return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    public static function FormatPrice($PRICE) {
        return number_format($PRICE, 0, '&nbsp;', '&nbsp;');
    }

    public static function Translit($STRING) {
        $params = array("replace_space" => "-", "replace_other" => "-");
        $result = CUtilEx::translit($STRING, "ru", $params);
        return $result;
    }

}
