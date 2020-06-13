<?

namespace LongByte;

use \Bitrix\Main\Loader;
use \Bitrix\Main\Config\Option;
use \Bitrix\Main\Data\Cache;
use \Bitrix\Highloadblock as HL;
use \Bitrix\Iblock\ElementTable;
use \Bitrix\Iblock\SectionTable;
use \Bitrix\Iblock\PropertyTable;
use \Bitrix\Iblock\PropertyEnumerationTable;

class Metatags {

    private $strModuleName = 'longbyte.metatags';
    private $strCurPage = '';
    private $arRules = false;
    private $iIblockID = 0;
    private $iMetatagsIblockID = 0;
    private $arResultCatalog = array();
    private $arCurSection = array();

    /**
     * 
     * @global type $APPLICATION
     * @param intereg $iIBlockID
     * @param array $arResult
     */
    function __construct($iIBlockID, $arResult) {
        global $APPLICATION;
        Loader::includeModule('iblock');
        $this->strCurPage = $APPLICATION->GetCurPage();
        $this->iIblockID = $this->iIblockID = $iIBlockID;
        $this->iMetatagsIblockID = Option::get($this->strModuleName, 'metatags_iblock', IBLOCK_SEO_TEXTS_LONGBYTE_METATAGS);    //2do in future
        $this->arResultCatalog = $arResult;
        $this->clearUrlEnding();
        $this->getCurSection();
        $this->getRule();
    }

    /**
     * Производит редирект при "лишнем" окончании в url
     */
    private function clearUrlEnding() {

        if (preg_match('/filter\/[^\/]*$/', $this->strCurPage)) {
            $strUrl = preg_replace('/filter\/([^\/]*)$/', '$1', $this->strCurPage);
            \LocalRedirect($strUrl, false, '301 Moved permanently');
        }
        if (preg_match('/filter\/clear\/[^\/]*$/', $this->strCurPage)) {
            $strUrl = preg_replace('/filter\/clear\/([^\/]*)$/', '$1', $this->strCurPage);
            \LocalRedirect($strUrl, false, '301 Moved permanently');
        }
    }

    /**
     * Получает текущий раздел
     * @global type $CACHE_MANAGER
     */
    private function getCurSection() {
        $arCurSection = array();

        $arFilter = array(
            "IBLOCK_ID" => $this->iIblockID,
            "ACTIVE" => "Y",
            "GLOBAL_ACTIVE" => "Y",
        );

        if (0 < intval($this->arResultCatalog["VARIABLES"]["SECTION_ID"]))
            $arFilter["ID"] = $this->arResultCatalog["VARIABLES"]["SECTION_ID"];
        elseif ('' != $this->arResultCatalog["VARIABLES"]["SECTION_CODE"])
            $arFilter["=CODE"] = $this->arResultCatalog["VARIABLES"]["SECTION_CODE"];

        if (!empty($arFilter["ID"]) || !empty($arFilter["=CODE"])) {
            $obCache = Cache::createInstance();
            $iCacheLifetime = 24 * 60 * 60;
            $strCacheID = 'MetaSection';
            $strCachePath = '/' . $strCacheID . $this->strCurPage;

            if ($obCache->initCache($iCacheLifetime, $strCacheID, $strCachePath)) {
                $arCurSection = $obCache->getVars();
            } elseif ($obCache->startDataCache()) {
                $arCurSection = array();
                $dbRes = SectionTable::getList(array(
                        'select' => array("ID", 'NAME', 'CODE'),
                        'filter' => $arFilter,
                        'limit' => 1
                ));

                if (defined("BX_COMP_MANAGED_CACHE")) {
                    global $CACHE_MANAGER;
                    $CACHE_MANAGER->StartTagCache("/iblock/catalog");

                    if ($arCurSection = $dbRes->fetch())
                        $CACHE_MANAGER->RegisterTag("iblock_id_" . $arParams["IBLOCK_ID"]);

                    $CACHE_MANAGER->EndTagCache();
                }
                else {
                    if (!$arCurSection = $dbRes->fetch())
                        $arCurSection = array();
                }
                $obCache->endDataCache($arCurSection);
            }
        }
        if (!isset($arCurSection))
            $arCurSection = array();

        $this->arCurSection = $arCurSection;
    }

    /**
     * Получает человеческое значение свойства
     * @param string $propCode
     * @param string $propValue
     * @return boolean/array
     */
    private function getPropValue($propCode, $propValue) {
        $arProp = PropertyTable::getList(array(
                'select' => array('ID', 'PROPERTY_TYPE', 'USER_TYPE', 'USER_TYPE_SETTINGS', 'LINK_IBLOCK_ID'),
                'filter' => array('IBLOCK_ID' => $this->iIblockID, 'CODE' => $propCode),
                'limit' => 1
            ))->fetch();

        if (!$arProp)
            return false;

        if (!empty($arProp['USER_TYPE_SETTINGS'])) {
            $arProp['USER_TYPE_SETTINGS'] = unserialize($arProp['USER_TYPE_SETTINGS']);
        }

        $arPropValues = explode('-or-', $propValue);
        $arValues = array();

        foreach ($arPropValues as $propValue) {
            switch ($arProp['PROPERTY_TYPE']) {
                case PropertyTable::TYPE_STRING:
                case PropertyTable::TYPE_NUMBER:
                    if ($arProp['USER_TYPE'] == 'directory') {
                        $hlblock = HL\HighloadBlockTable::getList(array(
                                "filter" => array("TABLE_NAME" => $arProp['USER_TYPE_SETTINGS']['TABLE_NAME'])  //проверить
                            ))->fetch();
                        $entity = HL\HighloadBlockTable::compileEntity($hlblock);
                        $entity_data_class = $entity->getDataClass();
                        $arLink = $entity_data_class::getList(array(
                                "select" => array("UF_NAME"),
                                'filter' => array('UF_XML_ID' => $propValue)
                            ))->fetch();
                        if ($arLink) {
                            $arValues[] = $arLink['UF_NAME'];
                        }
                    } else {
                        $arValues[] = $propValue;
                    }
                    break;
                case PropertyTable::TYPE_ELEMENT:
                    $arLinkElement = ElementTable::getList(array(
                            'select' => array('NAME'),
                            'filter' => array('IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'], '=CODE' => $propValue),
                            'limit' => 1
                        ))->fetch();

                    $arValues[] = $arLinkElement['NAME'];
                    break;
                case PropertyTable::TYPE_SECTION:
                    $arLinkSection = SectionTable::getList(array(
                            'select' => array('NAME'),
                            'filter' => array('IBLOCK_ID' => $arProp['LINK_IBLOCK_ID'], 'ID' => $propValue),
                            'limit' => 1
                        ))->fetch();

                    $arValues[] = $arLinkSection['NAME'];
                    break;
                case PropertyTable::TYPE_LIST:
                    $arEnumValue = PropertyEnumerationTable::getList(array(
                            'select' => array('VALUE'),
                            'filter' => array('PROPERTY_ID' => $arProp['ID'], 'XML_ID' => $propValue),
                            'limit' => 1
                        ))->fetch();
                    $arValues[] = $arEnumValue['VALUE'];
            }
        }
        if (count($arValues) <= 0)
            return false;

        return implode(', ', $arValues);
    }

    /**
     * Получение правила установке мета-тегов
     * @global type $USER
     */
    private function getRule() {
        global $USER;

        $obCache = Cache::createInstance();
        $iCacheLifetime = 24 * 60 * 60;
        $strCacheID = 'MetaOverride';
        $strCachePath = '/' . $strCacheID . $this->strCurPage;
        $arMetaOverride = false;
        if ($obCache->initCache($iCacheLifetime, $strCacheID, $strCachePath)) {
            $arMetaOverride = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {

            if ($obMetaOverride = \CIBlockElement::GetList(
                    array('SORT' => 'ASC'), //
                    array('IBLOCK_ID' => $this->iMetatagsIblockID, 'ACTIVE' => 'Y', 'GLOBAL_SECTION_ACTIVE' => 'Y', 'DATE_ACTIVE' => 'Y', '=CODE' => $this->strCurPage), //
                    false, //
                    array('nTopCount' => 1), //
                    array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_TEXT', 'CODE')
                )->GetNextElement()) {
                $arMetaOverrideFields = $obMetaOverride->GetFields();
                $arMetaOverrideProps = $obMetaOverride->GetProperties();
            }

            if (!$arMetaOverrideFields) {
                $rsMetaRegexp = \CIBlockElement::GetList(
                        array('SORT' => 'ASC'), //
                        array("IBLOCK_ID" => $this->iMetatagsIblockID, 'ACTIVE' => 'Y', 'GLOBAL_SECTION_ACTIVE' => 'Y', 'DATE_ACTIVE' => 'Y'), //
                        false, //
                        false, //
                        array('ID', 'IBLOCK_ID', 'NAME', 'DETAIL_TEXT', 'CODE')
                );

                while ($obMetaOverride = $rsMetaRegexp->GetNextElement()) {
                    $arMetaOverrideFields = $obMetaOverride->GetFields();
                    $arMetaOverrideProps = $obMetaOverride->GetProperties();
                    //генерируем RegExp
                    $regexp = '/^' . str_replace(array(
                            '/', '+', '*',
                            ), array(
                            '\\/', '[^\\/]+', '.+'
                            ), $arMetaOverrideFields['~CODE']) . '$/';

                    if (preg_match($regexp, $this->strCurPage, $matches)) {
                        if ($USER->IsAdmin() && $_REQUEST['dev'] == 'meta') {
                            echo "regexp:<pre>";
                            print_r($regexp);
                            echo "</pre>";
                            echo "matches:<pre>";
                            print_r($matches);
                            echo "</pre>";
                        }

                        $cntProps = (count($matches) - 1) / 2;
                        $arRuleProps = array();
                        $arReplaces = array();
                        for ($i = 1; $i <= $cntProps; $i++) {
                            $arRuleProps[strtoupper($matches[$i * 2 - 1])] = $matches[$i * 2];
                        }

                        foreach ($arRuleProps as $propCode => $propValue) {

                            $value = $this->getPropValue($propCode, $propValue);
                            if (!$value)
                                continue;

                            $arReplaces['{' . strtoupper($propCode) . '}'] = mb_strtoupper($value, 'UTF-8');
                            $arReplaces['{' . strtoupper(substr($propCode, 0, 1)) . strtolower(substr($propCode, 1)) . '}'] = mb_strtoupper(mb_substr($value, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($value, 1, 1000, 'UTF-8'), 'UTF-8');
                            $arReplaces['{' . strtolower($propCode) . '}'] = mb_strtolower($value, 'UTF-8');

                            unset($value);
                        }

                        if (!empty($this->arCurSection)) {
                            $arReplaces['{SECTION}'] = mb_strtoupper($this->arCurSection['NAME'], 'UTF-8');
                            $arReplaces['{Section}'] = mb_strtoupper(mb_substr($this->arCurSection['NAME'], 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($this->arCurSection['NAME'], 1, 1000, 'UTF-8'), 'UTF-8');
                            $arReplaces['{section}'] = mb_strtolower($this->arCurSection['NAME'], 'UTF-8');
                        } else {
                            $arReplaces['{SECTION}'] = '';
                            $arReplaces['{Section}'] = '';
                            $arReplaces['{section}'] = '';
                        }

                        if ($USER->IsAdmin() && $_REQUEST['dev'] == 'meta') {
                            echo "arReplaces:<pre>";
                            print_r($arReplaces);
                            echo "</pre>";
                        }
                        $arMetaOverrideFields = str_replace(array_keys($arReplaces), array_values($arReplaces), $arMetaOverrideFields);
                        foreach ($arMetaOverrideProps as &$over) {
                            $over = str_replace(array_keys($arReplaces), array_values($arReplaces), $over);
                        }
                        unset($over);

                        $arMetaOverride = array(
                            'BREAD_NAME' => $arMetaOverrideProps['BREAD_NAME']['VALUE'],
                            'H1' => $arMetaOverrideProps['H1']['VALUE'],
                            'TITLE' => $arMetaOverrideProps['TITLE']['VALUE'],
                            'KEYWORDS' => $arMetaOverrideProps['KEYWORDS']['VALUE'],
                            'DESCRIPTION' => $arMetaOverrideProps['DESCRIPTION']['VALUE'],
                            'SEO_TEXT' => $arMetaOverrideFields['DETAIL_TEXT'],
                        );

                        break;
                    } else {
                        $arMetaOverride = false;
                    }
                    unset($matches, $arRuleProps, $arReplaces);
                }
            }

            $obCache->endDataCache($arMetaOverride);
        }

        $this->arRules = $arMetaOverride;
    }

    /**
     * Устанавливает параметры страницы
     * @global $APPLICATION
     */
    public function showMetatags() {

        global $APPLICATION;

        if ($this->arRules) {
            if (!empty($this->arRules["BREAD_NAME"])) {
                $APPLICATION->AddChainItem($this->arRules["BREAD_NAME"], $this->strCurPage);
            }

            if (!empty($this->arRules["TITLE"])) {
                $APPLICATION->SetPageProperty("title", $this->arRules["TITLE"]);
            }

            if (!empty($this->arRules["H1"])) {
                $APPLICATION->SetTitle($this->arRules["H1"]);
            }

            if (!empty($this->arRules["KEYWORDS"])) {
                $APPLICATION->SetPageProperty("keywords", $this->arRules["KEYWORDS"]);
            }

            if (!empty($this->arRules["DESCRIPTION"])) {
                $APPLICATION->SetPageProperty("description", $this->arRules["DESCRIPTION"]);
            }
        }
    }

    /**
     * Возвращает seo текст
     * @return string
     */
    public function getSeoText() {
        return $this->arRules['SEO_TEXT'];
    }

}
