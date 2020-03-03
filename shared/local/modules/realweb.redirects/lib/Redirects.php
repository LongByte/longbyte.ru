<?php

namespace Realweb\Redirects;

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Uri;
use Bitrix\Highloadblock\HighloadBlockTable;

class Redirects {

    protected static $arHlBlock;

    /**
     * 
     * @param \Bitrix\Main\ORM\Event $obEvent
     */
    public static function onBeforeSave($obEvent) {
        $arFields = $obEvent->getParameter('fields');

        if (!$arFields['UF_REGEXP']) {
            $obUriFrom = new Uri($arFields['UF_FROM']);
            $obUriTo = new Uri($arFields['UF_TO']);

            $obResult = new \Bitrix\Main\ORM\EventResult();
            $obResult->modifyFields(array(
                'UF_FROM' => $obUriFrom->getPath(),
                'UF_TO' => $obUriTo->getPath(),
            ));

            $obEvent->addResult($obResult);
        }
    }

    public static function onPageStart() {
        if (Loader::includeModule('iblock') && Loader::includeModule('highloadblock')) {
            self::checkRedirects();
        }
    }

    private static function checkRedirects() {

        if (!self::$arHlBlock) {
            self::$arHlBlock = HighloadBlockTable::getRow(array(
                    'filter' => array(
                        'TABLE_NAME' => 'realweb_redirects',
                    ))
            );
        }

        if (self::$arHlBlock) {

            $obUri = new Uri(Context::getCurrent()->getRequest()->getRequestUri());

            /* @var $obEntity \Bitrix\Main\ORM\Entity */
            $obEntity = HighloadBlockTable::compileEntity(self::$arHlBlock);
            $obDataClass = $obEntity->getDataClass();

            $arRedirect = $obDataClass::getRow(array(
                    'filter' => array(
                        'UF_ACTIVE' => 1,
                        'UF_FROM' => $obUri->getPath(),
                        'UF_REGEXP' => 0,
                    ),
                    'order' => array(
                        'UF_SORT' => 'ASC'
                    ),
            ));

            if ($arRedirect) {
                if (strlen($arRedirect['UF_TO']) > 0 && $arRedirect['UF_FROM'] != $arRedirect['UF_TO']) {
                    LocalRedirect($arRedirect['UF_TO'], false, '301 Moved permanently');
                }
            }

            if (!$arRedirect) {
                $rsRedirects = $obDataClass::getList(array(
                        'filter' => array(
                            'UF_ACTIVE' => 1,
                            'UF_REGEXP' => 1,
                        ),
                        'order' => array(
                            'UF_SORT' => 'ASC'
                        ),
                ));

                while ($arRedirect = $rsRedirects->fetch()) {
                    if (strlen($arRedirect['UF_TO']) > 0 && preg_match($arRedirect['UF_FROM'], $obUri->getPathQuery())) {
                        $strUriTo = preg_replace($arRedirect['UF_FROM'], $arRedirect['UF_TO'], $obUri->getPathQuery());
                        LocalRedirect($strUriTo, false, '301 Moved permanently');
                    }
                }
            }
        }
    }

}
