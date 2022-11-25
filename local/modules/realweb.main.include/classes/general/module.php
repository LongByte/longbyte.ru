<?php

namespace Realweb;

IncludeModuleLangFile(__FILE__);

class MainInclude
{

    public static function addCategory($arData)
    {
        $arCategory = \Realweb\RealwebMainIncludeCategoryTable::getByCode($arData['CODE']);
        if (empty($arCategory)) {
            \Realweb\RealwebMainIncludeCategoryTable::add($arCategory);
        } else {
            \Realweb\RealwebMainIncludeCategoryTable::update($arCategory['ID'], $arData);
        }
    }

}
