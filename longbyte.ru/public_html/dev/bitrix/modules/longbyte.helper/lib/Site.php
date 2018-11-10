<?

namespace LongByte;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;

class Site {

    public static function Definders() {

        if (Loader::includeModule('iblock')) {

            $rsIBlocks = \Bitrix\Iblock\IblockTable::getList(array(
                    'select' => array('ID', 'IBLOCK_TYPE_ID', 'CODE'),
            ));
            while ($arIblock = $rsIBlocks->fetch()) {
                $arIblock['CODE'] = str_replace('-', '_', $arIblock['CODE']);
                $CONSTANT = ToUpper(implode('_', array('IBLOCK', $arIblock['IBLOCK_TYPE_ID'], $arIblock['CODE'])));
                if (!defined($CONSTANT)) {
                    define($CONSTANT, $arIblock['ID']);
                }
            }
        }

        if (Loader::includeModule('form')) {
            $rsForms = \CForm::GetList();
            while ($arForm = $rsForms->fetch()) {
                $arForm['SID'] = str_replace('-', '_', $arForm['SID']);
                $CONSTANT = ToUpper(implode('_', array('FORM', $arForm['SID'])));
                if (!defined($CONSTANT)) {
                    define($CONSTANT, $arForm['ID']);
                }
            }
        }
    }

    public static function DeclOfNum($number, $titles) {
        $cases = array(2, 0, 1, 1, 1, 2);
        return $titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]];
    }

    public static function Translit($STRING) {
        $params = array("replace_space" => "-", "replace_other" => "-");
        $result = Cutil::translit($STRING, "ru", $params);
        return $result;
    }

    /**
     * Проверка безопасного подключения
     * @return bool
     */
    public static function isHttps() {
        $bIsHttps = false;
        try {
            $bIsHttps = Application::getInstance()->getContext()->getRequest()->isHttps();
        } catch (Exception $exception) {
            
        }

        return $bIsHttps;
    }

    public static function getDomain() {
        return (self::isHttps() ? 'https' : 'http') . '://' . $_SERVER['SERVER_NAME'];
    }

}
