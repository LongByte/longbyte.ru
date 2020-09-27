<?

namespace LongByte;

use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Web\Json;

class Vue {

    public static function includeVueJS() {
        if (\Site::IsDevelop() || true) {
            Asset::getInstance()->addString('<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>');
        } else {
            Asset::getInstance()->addString('<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>');
        }
    }

    /**
     * 
     * @param array $arData
     * @return string
     */
    public static function toJson($arData) {
        self::arrayKeyToLower($arData);
        return Json::encode($arData);
    }
    
    /**
     * 
     * @param array $arData
     * @return string
     */
    public static function toVueJson($arData) {
        self::arrayKeyToLower($arData);
        return \CUtil::PhpToJSObject($arData, false, true, true);
    }

    /**
     * 
     * @param array $array
     */
    public static function arrayKeyToLower(array &$array) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                self::arrayKeyToLower($value);
            }
            unset($array[$key]);
            $key = strtolower($key);
            $array[$key] = $value;
        }
    }

}
