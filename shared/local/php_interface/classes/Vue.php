<?

namespace LongByte;

use \Bitrix\Main\Page\Asset;

class Vue {

    public static function includeVueJS() {
        if (\Site::IsDevelop()) {
            Asset::getInstance()->addString('<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>');
        } else {
            Asset::getInstance()->addString('<script src="https://cdn.jsdelivr.net/npm/vue"></script>');
        }
    }

    public static function toVueJson($arData) {
        self::arrayKeyToLower($arData);
        return \CUtil::PhpToJSObject($arData, false, true, true);
    }

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
