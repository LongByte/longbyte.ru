<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/** @var CBitrixComponent $this */
/** @var array $arParams */
/** @var array $arResult */
/** @var string $componentPath */
/** @var string $componentName */
/** @var string $componentTemplate */
/** @global CDatabase $DB */
/** @global CUser $USER */
/** @global CMain $APPLICATION */
if (!isset($arParams["CACHE_TIME"]))
    $arParams["CACHE_TIME"] = 36000000;

$arParams["ID"] = intval($arParams["ID"]);
$arParams["IBLOCK_ID"] = intval($arParams["IBLOCK_ID"]);

$arParams["DEPTH_LEVEL"] = intval($arParams["DEPTH_LEVEL"]);
if ($arParams["DEPTH_LEVEL"] <= 0)
    $arParams["DEPTH_LEVEL"] = 1;

$arResult["SECTIONS"] = array();
$arResult["ELEMENT_LINKS"] = array();
$arResult["ELEMENTS"] = array();

if (strlen($arParams["ELEMENT_FILTERNAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["ELEMENT_FILTERNAME"])) {
    $arrElementFilter = array();
} else {
    $arrElementFilter = $GLOBALS[$arParams["ELEMENT_FILTERNAME"]];
    if (!is_array($arrElementFilter))
        $arrElementFilter = array();
}
if (strlen($arParams["SECTION_FILTERNAME"]) <= 0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["SECTION_FILTERNAME"])) {
    $arrSectionFilter = array();
} else {
    $arrSectionFilter = $GLOBALS[$arParams["SECTION_FILTERNAME"]];
    if (!is_array($arrSectionFilter))
        $arrSectionFilter = array();
}

if (!isset($arParams['ROOT_ELEMENTS'])) {
    $arParams["ROOT_ELEMENTS"] = "Y";
}

if ($this->StartResultCache(false, array($arrElementFilter, $arrSectionFilter))) {
    if (!CModule::IncludeModule("iblock")) {
        $this->AbortResultCache();
    } else {

        $EXIST_ELEMENTS = array();
        //сначала корневые элементы
        $arSelectRootElements = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID", "NAME", "SORT");
        if (isset($arParams['EXTERNAL_LINK_PROPERTY']) && strlen($arParams['EXTERNAL_LINK_PROPERTY']) > 0) {
            $arSelectRootElements[] = 'PROPERTY_' . $arParams['EXTERNAL_LINK_PROPERTY'];
        }
        if (isset($arParams['ELEMENTS_SELECT']) && is_array($arParams['ELEMENTS_SELECT']) > 0) {
            $arSelectRootElements = array_merge($arSelectRootElements, $arParams['ELEMENTS_SELECT']);
        }
        $arFilterRootElements = array(
            "ACTIVE" => "Y",
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
        );
        if ($arParams["ROOT_ELEMENTS"] == "Y") {
            $arFilterRootElements['SECTION_ID'] = false;
        }

        $rsElements = CIBlockElement::GetList(array('SORT' => 'ASC'), array_merge($arFilterRootElements, $arrElementFilter), false, false, $arSelectRootElements);
        if (($arParams["IS_SEF"] === "Y") && (strlen($arParams["DETAIL_PAGE_URL"]) > 0)) {
            $rsElements->SetUrlTemplates($arParams["SEF_BASE_URL"] . $arParams["DETAIL_PAGE_URL"]);
        }
        while ($arElement = $rsElements->GetNext()) {
            if (is_array($arParams['EXTERNAL_LINK'])) {
                if (is_callable($arParams['EXTERNAL_LINK'])) {
                    $arElement = call_user_func_array($arParams['EXTERNAL_LINK'], array($arElement, $arParams));
                }
            }

            if ($arElement['CODE'] == '#') {
                if (($arParams["IS_SEF"] === "Y") && (strlen($arParams["DETAIL_PAGE_URL"]) > 0)) {
                    $arElement["~DETAIL_PAGE_URL"] = $arElement["DETAIL_PAGE_URL"] = $arParams["SEF_BASE_URL"];
                } else {
                    $arElement["~DETAIL_PAGE_URL"] = $arElement["DETAIL_PAGE_URL"] = str_replace(urlencode('#') . '/', "", $arElement["~DETAIL_PAGE_URL"]);
                }
            }
            $PROPS = array();
            if (isset($arParams['ELEMENTS_SELECT']) && is_array($arParams['ELEMENTS_SELECT']) > 0) {
                $PROPS = array();
                foreach ($arElement as $arElementFIELD => $FIELD_VALUE) {

                    foreach ($arParams['ELEMENTS_SELECT'] as $PROPERTY) {
                        if (strpos($arElementFIELD, $PROPERTY . "_") !== false) {
                            $CLEAN_PROP = str_replace($PROPERTY . "_", "", $arElementFIELD);
                            $CLEAN_KEY = str_replace("PROPERTY_", "", $PROPERTY);
                            if (!isset($PROPS[$CLEAN_KEY])) {
                                $PROPS[$CLEAN_KEY] = array();
                            }
                            $PROPS[$CLEAN_KEY][$CLEAN_PROP] = $FIELD_VALUE;
                        }
                    }
                }
            }
            if (!in_array($arElement["ID"], $EXIST_ELEMENTS)) {
                $arResult["SECTIONS"][] = array(
                    "ID" => $arElement["ID"],
                    "CODE" => $arElement["CODE"],
                    "PICTURE" => $arElement["PREVIEW_PICTURE"],
                    "DEPTH_LEVEL" => 1,
                    "~NAME" => $arElement["~NAME"],
                    "~DETAIL_PAGE_URL" => $arElement["~DETAIL_PAGE_URL"],
                    "SORT" => intval($arElement["SORT"]),
                    "IBLOCK_SECTION_ID" => 0,
                    'URL' => (strpos($arElement["CODE"], "http://") !== false || strpos($arElement["CODE"], "https://") !== false ? $arElement["CODE"] : $arElement["~DETAIL_PAGE_URL"]),
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "IS_SECTION" => false,
                    "IS_ELEMENT" => true,
                    'PROPS' => $PROPS,
                );
                $EXIST_ELEMENTS[] = $arElement["ID"];
            }
        }


        $arFilter = array(
            "IBLOCK_ID" => $arParams["IBLOCK_ID"],
            "GLOBAL_ACTIVE" => "Y",
            "IBLOCK_ACTIVE" => "Y",
            "<=" . "DEPTH_LEVEL" => $arParams["DEPTH_LEVEL"],
        );
        $arOrder = array(
            "left_margin" => "asc",
        );

        $rsSections = CIBlockSection::GetList($arOrder, array_merge($arFilter, $arrSectionFilter), false, array(
            "ID",
            "DEPTH_LEVEL",
            "NAME",
            "SECTION_PAGE_URL",
            "SORT",
            "UF_*",
            "IBLOCK_SECTION_ID",
            "PICTURE",
        ));
        if ($arParams["IS_SEF"] !== "Y")
            $rsSections->SetUrlTemplates("", $arParams["SECTION_URL"]);
        else
            $rsSections->SetUrlTemplates("", $arParams["SEF_BASE_URL"] . $arParams["SECTION_PAGE_URL"]);
        while ($arSection = $rsSections->GetNext()) {
            $UF = array();
            foreach ($arSection as $arSectionFieldName => $arSectionFieldValue) {
                if (strpos($arSectionFieldName, 'UF_') !== false && strpos($arSectionFieldName, '~UF_') === false) {
                    $UF[$arSectionFieldName] = $arSectionFieldValue;
                }
            }
            $arResult["SECTIONS"][] = array(
                "ID" => $arSection["ID"],
                "CODE" => $arSection["CODE"],
                "DEPTH_LEVEL" => $arSection["DEPTH_LEVEL"],
                "~NAME" => $arSection["~NAME"],
                "~SECTION_PAGE_URL" => $arSection["~SECTION_PAGE_URL"],
                'URL' => (strpos($arSection["CODE"], "http://") !== false || strpos($arSection["CODE"], "https://") !== false ? $arSection["CODE"] : $arSection["~SECTION_PAGE_URL"]),
                "UF" => $UF,
                "SORT" => intval($arSection["SORT"]),
                "IBLOCK_SECTION_ID" => intval($arSection["IBLOCK_SECTION_ID"]),
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "PICTURE" => $arSection["PICTURE"],
                "IS_SECTION" => true,
                "IS_ELEMENT" => false,
                "PROPS" => array(),
            );
            $arResult["ELEMENT_LINKS"][$arSection["ID"]] = array();
            $arResult["ELEMENTS"][$arSection["ID"]] = array();
            if (intval($arSection["DEPTH_LEVEL"]) < intval($arParams["DEPTH_LEVEL"])) {
                //проверим, надо ли включать элементы?
                if (isset($arParams['SECTION_USER_FIELD_TO_NOT_INCLUDE_INSIDE_ELEMENTS']) && strlen($arParams['SECTION_USER_FIELD_TO_NOT_INCLUDE_INSIDE_ELEMENTS']) > 0) {
                    if (isset($arSection[$arParams['SECTION_USER_FIELD_TO_NOT_INCLUDE_INSIDE_ELEMENTS']]) && intval($arSection[$arParams['SECTION_USER_FIELD_TO_NOT_INCLUDE_INSIDE_ELEMENTS']]) > 0) {
                        continue;
                    }
                }

                //добавим элементы
                $arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID", "NAME", "SORT");
                if (isset($arParams['EXTERNAL_LINK_PROPERTY']) && strlen($arParams['EXTERNAL_LINK_PROPERTY']) > 0) {
                    $arSelect[] = 'PROPERTY_' . $arParams['EXTERNAL_LINK_PROPERTY'];
                }
                if (isset($arParams['ELEMENTS_SELECT']) && is_array($arParams['ELEMENTS_SELECT']) > 0) {
                    $arSelect = array_merge($arSelect, $arParams['ELEMENTS_SELECT']);
                }

                if (intval($arSection["ID"]) <= 0) {
                    continue;
                }


                $arFilter = array(
                    "SECTION_ID" => (intval($arSection["ID"]) > 0 ? $arSection["ID"] : 0),
                    "ACTIVE" => "Y",
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "INCLUDE_SUBSECTIONS" => 'N',
                );

                $rsElements = CIBlockElement::GetList(array('SORT' => 'ASC'), array_merge($arFilter, $arrElementFilter), false, false, $arSelect);
                if (($arParams["IS_SEF"] === "Y") && (strlen($arParams["DETAIL_PAGE_URL"]) > 0)) {
                    $rsElements->SetUrlTemplates($arParams["SEF_BASE_URL"] . $arParams["DETAIL_PAGE_URL"]);
                }
                while ($arElement = $rsElements->GetNext()) {
                    if (is_array($arParams['EXTERNAL_LINK'])) {
                        if (is_callable($arParams['EXTERNAL_LINK'])) {
                            $arElement = call_user_func_array($arParams['EXTERNAL_LINK'], array($arElement, $arParams));
                        }
                    }
                    $arResult["ELEMENT_LINKS"][$arElement["IBLOCK_SECTION_ID"]][] = $arElement["~DETAIL_PAGE_URL"];
                    $arElement["IBLOCK_SECTION_ID"] = $arSection["ID"];
                    if (intval($arElement["IBLOCK_SECTION_ID"]) == intval($arSection["ID"])) {
                        if (!isset($arResult["ELEMENTS"][$arElement["IBLOCK_SECTION_ID"]][$arElement["ID"]])) {
                            $PROPS = array();
                            if (isset($arParams['ELEMENTS_SELECT']) && is_array($arParams['ELEMENTS_SELECT']) > 0) {
                                $PROPS = array();
                                foreach ($arElement as $arElementFIELD => $FIELD_VALUE) {

                                    foreach ($arParams['ELEMENTS_SELECT'] as $PROPERTY) {
                                        if (strpos($arElementFIELD, $PROPERTY . "_") !== false) {
                                            $CLEAN_PROP = str_replace($PROPERTY . "_", "", $arElementFIELD);
                                            $CLEAN_KEY = str_replace("PROPERTY_", "", $PROPERTY);
                                            if (!isset($PROPS[$CLEAN_KEY])) {
                                                $PROPS[$CLEAN_KEY] = array();
                                            }
                                            $PROPS[$CLEAN_KEY][$CLEAN_PROP] = $FIELD_VALUE;
                                        }
                                    }
                                }
                            }

                            $arResult["ELEMENTS"][$arElement["IBLOCK_SECTION_ID"]][$arElement["ID"]] = array(
                                "ID" => $arElement["ID"],
                                "CODE" => $arElement["CODE"],
                                "PICTURE" => $arElement["PREVIEW_PICTURE"],
                                "DEPTH_LEVEL" => intval($arSection["DEPTH_LEVEL"]) + 1,
                                "~NAME" => $arElement["~NAME"],
                                "~DETAIL_PAGE_URL" => $arElement["~DETAIL_PAGE_URL"],
                                "SORT" => intval($arElement["SORT"]),
                                "IBLOCK_SECTION_ID" => intval($arElement["IBLOCK_SECTION_ID"]),
                                'URL' => (strpos($arElement["CODE"], "http://") !== false || strpos($arElement["CODE"], "https://") !== false ? $arElement["CODE"] : $arElement["~DETAIL_PAGE_URL"]),
                                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                                "IS_SECTION" => false,
                                "IS_ELEMENT" => true,
                                'PROPS' => $PROPS,
                            );
                        }
                    }
                }
            }
        }
        if (defined("BX_COMP_MANAGED_CACHE")) {
            global $CACHE_MANAGER;
            $CACHE_MANAGER->registerTag('iblock_id_' . $arParams["IBLOCK_ID"]);
            $CACHE_MANAGER->EndTagCache();
        }

        $this->EndResultCache();
    }
}

//In "SEF" mode we'll try to parse URL and get ELEMENT_ID from it
if ($arParams["IS_SEF"] === "Y") {
    $engine = new CComponentEngine($this);
    if (CModule::IncludeModule('iblock')) {
        $engine->addGreedyPart("#SECTION_CODE_PATH#");
        $engine->setResolveCallback(array("CIBlockFindTools", "resolveComponentEngine"));
    }
    $componentPage = $engine->guessComponentPath(
        $arParams["SEF_BASE_URL"], array(
        "section" => $arParams["SECTION_PAGE_URL"],
        "detail" => $arParams["DETAIL_PAGE_URL"],
    ), $arVariables
    );
    if ($componentPage === "detail") {
        CComponentEngine::InitComponentVariables(
            $componentPage, array("SECTION_ID", "ELEMENT_ID"), array(
            "section" => array("SECTION_ID" => "SECTION_ID"),
            "detail" => array("SECTION_ID" => "SECTION_ID", "ELEMENT_ID" => "ELEMENT_ID"),
        ), $arVariables
        );
        $arParams["ID"] = intval($arVariables["ELEMENT_ID"]);
    }
}


if (($arParams["ID"] > 0) && (intval($arVariables["SECTION_ID"]) <= 0) && CModule::IncludeModule("iblock")) {
    $arSelect = array("ID", "IBLOCK_ID", "DETAIL_PAGE_URL", "IBLOCK_SECTION_ID");
    $arFilter = array(
        "ID" => $arParams["ID"],
        "ACTIVE" => "Y",
        "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    );
    $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
    if (($arParams["IS_SEF"] === "Y") && (strlen($arParams["DETAIL_PAGE_URL"]) > 0))
        $rsElements->SetUrlTemplates($arParams["SEF_BASE_URL"] . $arParams["DETAIL_PAGE_URL"]);
    while ($arElement = $rsElements->GetNext()) {
        $arResult["ELEMENT_LINKS"][$arElement["IBLOCK_SECTION_ID"]][] = $arElement["~DETAIL_PAGE_URL"];
    }
}

$arResult['MENU_TREE'] = array();
foreach ($arResult['SECTIONS'] as &$arSection) {
    if (intval($arSection['IBLOCK_SECTION_ID']) == 0) {
        $this->GetChilds($arSection, $arResult);
        $arResult['MENU_TREE'][] = $arSection;
    }
}
//отсортируем
usort($arResult['MENU_TREE'], function ($a, $b) {
    if (intval($a['SORT']) == intval($b['SORT'])) {
        return 0;
    }
    return (intval($a['SORT']) < intval($b['SORT'])) ? -1 : 1;
});

$aMenuLinksNew = array();
$menuIndex = 0;

foreach ($arResult["MENU_TREE"] as $arItem) {
    $this->ProcessMenu($arItem, $aMenuLinksNew, $menuIndex);
}

return $aMenuLinksNew;
?>
