<?

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Application;
use Bitrix\Main\IO;

class LongbyteVueComponent extends CBitrixComponent
{

    /**
     * Prepare Component Params
     * @param array $arParams
     * @return array
     */
    public function onPrepareComponentParams($arParams)
    {
        $arParams['INCLUDE_COMPONENTS'] = is_array($arParams['INCLUDE_COMPONENTS']) ? $arParams['INCLUDE_COMPONENTS'] : array();
        $arParams['STYLE_TO_COMPILER'] = $arParams['STYLE_TO_COMPILER'] == 'Y' ? 'Y' : 'N';

        return $arParams;
    }

    /**
     * Start Component
     * @global \CMain $APPLICATION
     */
    public function executeComponent()
    {
        global $APPLICATION;

        if (count($this->arParams['INCLUDE_COMPONENTS']) > 0) {
            foreach ($this->arParams['INCLUDE_COMPONENTS'] as $strComponent) {
                $APPLICATION->IncludeComponent("longbyte:vue", $strComponent, array(
                    'STYLE_TO_COMPILER' => $this->arParams['STYLE_TO_COMPILER'],
                ), $this->__parent ?: $this
                );
            }
        }

        $this->IncludeComponentTemplate();

        $obDir = new IO\Directory(Application::getDocumentRoot() . $this->__template->__folder . '/');
        $arFiles = $obDir->getChildren();
        foreach ($arFiles as $obFile) {
            if ($obFile->getExtension() == 'vue') {
                include_once $obFile->getPath();
            }
        }

        $arStyleExt = array(
            'sass',
            'less',
        );

        if ($this->arParams['STYLE_TO_COMPILER'] == 'Y') {
            foreach ($arStyleExt as $ext) {
                $obFile = new IO\File(Application::getDocumentRoot() . $this->__template->__folder . '/style.' . $ext);
                if ($obFile->isExists()) {
                    $APPLICATION->IncludeComponent(
                        "longbyte:longbyte.csscompiler.template", "less", array(
                        'TEMPLATE_PATH' => $this->__template->__folder . '/',
                    ), false, array(
                            "HIDE_ICONS" => "Y",
                        )
                    );
                    break;
                }
            }
        }
    }

}
