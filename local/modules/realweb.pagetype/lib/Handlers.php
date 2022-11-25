<?php

namespace Realweb\PageType;

use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;

class Handlers
{

    function OnPageStart()
    {

        $iblockType = 'content';
        $iblockCode = 'realweb_content';

        $constant = $CONSTANT = ToUpper(implode('_', array('IBLOCK', $iblockType, str_replace('-', '_', $iblockCode))));

        if (!defined($constant) && Loader::includeModule('iblock')) {

            $arIblock = IblockTable::getRow(array(
                'filter' => array('IBLOCK_TYPE_ID' => $iblockType, '=CODE' => $iblockCode),
            ));

            if ($arIblock) {
                define($constant, $arIblock['ID']);
            }
        }
    }

}
