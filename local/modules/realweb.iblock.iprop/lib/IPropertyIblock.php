<?php

namespace Realweb\IblockIprop;

use Bitrix\Main\Context;
use Bitrix\Main\Application;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\InheritedProperty;

class IPropertyIblock
{

    /**
     *
     * @param \CAdminTabControl $tabControl
     */
    public static function OnAdminTabControlBegin(&$tabControl)
    {

        $obRequest = Context::getCurrent()->getRequest();

        $iIblockId = (int) $obRequest->get('ID');
        $strUri = $obRequest->getRequestUri();

        if (strpos($strUri, '/bitrix/admin/iblock_edit.php') !== false && $iIblockId > 0) {

            $iSeoTabIndex = null;
            foreach ($tabControl->tabs as $keyTab => $arTab) {
                if ($arTab['TAB'] == 'SEO') {
                    $iSeoTabIndex = $keyTab;
                    break;
                }
            }

            $localPath = getLocalPath("");
            $bxRoot = strlen($localPath) > 0 ? rtrim($localPath, "/\\") : BX_ROOT;

            $obIpropTemlates = new InheritedProperty\IblockTemplates($iIblockId);
            $arIPropertyTemplates = $obIpropTemlates->findTemplates();

            ob_start();
            include(Application::getDocumentRoot() . $bxRoot . '/modules/realweb.iblock.iprop/templates/adminIblockSeo.php');
            $content = ob_get_contents();
            ob_end_clean();

            $arNewTab = array(
                'DIV' => 'seo2',
                'ICON' => 'iblock',
                'TAB' => Loc::getMessage('REALWEB.IBLOCK.IPROP.TAB_NAME'),
                'TITLE' => Loc::getMessage('REALWEB.IBLOCK.IPROP.TAB_TITLE'),
                'CONTENT' => $content,
            );

            if ($iSeoTabIndex !== null) {
                array_splice($tabControl->tabs, $iSeoTabIndex + 1, 0, array($arNewTab));
            } else {
                $tabControl->tabs[] = $arNewTab;
            }
        }
    }

    /**
     * @global \CCacheManager $CACHE_MANAGER
     *
     */
    public static function OnPageStart()
    {

        $obRequest = Context::getCurrent()->getRequest();
        $strUri = $obRequest->getRequestUri();

        if (strpos($strUri, '/bitrix/admin/iblock_edit.php') !== false) {
            $iIblockId = (int) $obRequest->get('ID');
            $arIPROPERTY_TEMPLATES = $obRequest->get('IPROPERTY_TEMPLATES');

            if ($iIblockId > 0 && is_array($arIPROPERTY_TEMPLATES)) {
                $arFields['IPROPERTY_TEMPLATES'] = array(
                    'IBLOCK_META_TITLE' => $arIPROPERTY_TEMPLATES['IBLOCK_META_TITLE']['TEMPLATE'],
                    'IBLOCK_META_KEYWORDS' => $arIPROPERTY_TEMPLATES['IBLOCK_META_KEYWORDS']['TEMPLATE'],
                    'IBLOCK_META_DESCRIPTION' => $arIPROPERTY_TEMPLATES['IBLOCK_META_DESCRIPTION']['TEMPLATE'],
                    'IBLOCK_PAGE_TITLE' => $arIPROPERTY_TEMPLATES['IBLOCK_PAGE_TITLE']['TEMPLATE'],
                );

                $obIblock = new \CIBlock();
                $obIblock->Update($iIblockId, $arFields);
                global $CACHE_MANAGER;
                $CACHE_MANAGER->ClearByTag("iblock_id_meta_" . $iIblockId);
            }
        }
    }

}
