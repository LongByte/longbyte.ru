<?

namespace LongByte;

use \Bitrix\Main\Page\Asset;
use \Bitrix\Main\Web\Json;

/**
 * Class \LongByte\Vue
 */
class Vue
{

    public static function includeVueJS(): void
    {
        if (\Site::IsDevelop()) {
            Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/vue-dev.js');
        } else {
            Asset::getInstance()->addJs(SITE_TEMPLATE_PATH . '/js/lib/vue.js');
        }
    }

    public static function toJson($arData): string
    {
        self::arrayKeyToLower($arData);
        return Json::encode($arData);
    }

    public static function toVueJson($arData): string
    {
        self::arrayKeyToLower($arData);
        return \CUtil::PhpToJSObject($arData, false, true, true);
    }

    public static function arrayKeyToLower(array &$array): void
    {
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
