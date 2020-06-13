<?php

class CRealwebMenuSectionsComponent extends CBitrixComponent {

    public function GetChilds(&$arSection, $arResult) {
        $arSection['CHILDS'] = array();
         if($arSection['IS_ELEMENT']){
            return;
        }
        $arSection['ELEMENT_LINKS'] = array();
        if (isset($arResult["ELEMENT_LINKS"][$arSection["ID"]])) {
            $arSection['ELEMENT_LINKS'] = $arResult["ELEMENT_LINKS"][$arSection["ID"]];
        }
        foreach ($arResult['SECTIONS'] as $arCheckSection) {
            if (intval($arCheckSection['IBLOCK_SECTION_ID']) == intval($arSection['ID'])) {
                $this->GetChilds($arCheckSection, $arResult);
                $arSection['CHILDS'][] = $arCheckSection;
            }
        }
        if (isset($arResult['ELEMENTS'][$arSection['ID']])) {
            $arSection['CHILDS'] = array_merge($arSection['CHILDS'], $arResult['ELEMENTS'][$arSection['ID']]);
        }
        //отсортируем
        usort($arSection['CHILDS'], function($a, $b) {
            if (intval($a['SORT']) == intval($b['SORT'])) {
                return 0;
            }
            return (intval($a['SORT']) < intval($b['SORT'])) ? -1 : 1;
        });
    }

    public function ProcessMenu($arItem, &$aMenuLinksNew, &$menuIndex) {
        $aMenuLinksNew[$menuIndex++] = array(
            htmlspecialcharsbx($arItem["~NAME"]),
            $arItem["URL"],
            $arItem["ELEMENT_LINKS"],
            array(
                "FROM_IBLOCK" => true,
                "IS_PARENT" => (isset($arItem['CHILDS']) && count($arItem['CHILDS']) > 0 ? true : false),
                "DEPTH_LEVEL" => $arItem["DEPTH_LEVEL"],
                "ID" => $arItem["ID"],
                "NAME" => htmlspecialcharsbx($arItem["~NAME"]),
                "CODE" => $arItem["CODE"],
                "UF" => (isset($arItem["UF"]) ? $arItem["UF"] : false),
                "SORT" => $arItem["SORT"],
                "PICTURE" => (isset($arItem["PICTURE"]) && intval($arItem["PICTURE"]) > 0 ? CFile::GetFileArray($arItem["PICTURE"]) : null),
                "IBLOCK_ID" => $arItem["IBLOCK_ID"],
                "IBLOCK_SECTION_ID" => $arItem["IBLOCK_SECTION_ID"],
                "IS_SECTION" => $arItem['IS_SECTION'],
                "IS_ELEMENT" => $arItem['IS_ELEMENT'],
                'PROPS' => $arItem["PROPS"],
            ),
        );

        if (isset($arItem['CHILDS']) && count($arItem['CHILDS']) > 0) {
            foreach($arItem['CHILDS'] as $arItemChild){
                $this->ProcessMenu($arItemChild, $aMenuLinksNew, $menuIndex);
            }
        }
    }

}
