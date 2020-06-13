<?

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

$module_id = 'longbyte.sitemap';
$moduleAccessLevel = $APPLICATION->GetGroupRight($module_id);
if ($moduleAccessLevel >= 'R') {
    Loader::includeModule($module_id);
    Loc::loadMessages(__FILE__);
}
?>